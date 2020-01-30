<?php

namespace App\Modules\User\Controller;

use App\Modules\PasswordReset\Model\Repository\PasswordResetRepository;
use App\Modules\Token\Helper\Token;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Framework\Exception\RepositoryException;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class PasswordChangeController
 * @package App\Modules\User\Controller
 */
class PasswordChangeController extends AbstractController
{
    protected $mandatoryParams = ['id', 'resetCode', 'email', 'newPassword'];

    /** @var PasswordResetRepository */
    protected $passwordResetRepository;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \App\Modules\Token\Helper\Token */
    protected $token;

    /** @var array */
    protected $settings;

    /**
     * PasswordChangeController constructor.
     * @param RepositoryInterface $repository
     * @param PasswordResetRepository $passwordResetRepository
     * @param LoggerInterface $logger
     * @param Token $token
     * @param array $settings
     */
    public function __construct(
        RepositoryInterface $repository,
        PasswordResetRepository $passwordResetRepository,
        LoggerInterface $logger,
        Token $token,
        array $settings
    ) {
        parent::__construct($repository);
        $this->passwordResetRepository = $passwordResetRepository;
        $this->logger = $logger;
        $this->token = $token;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function execute(Request $request, Response $response, $args = []): Response
    {
        $params = $this->getBodyJsonParams($request);
        $params = array_merge($params, $args);

        if (!empty($missingParams = $this->checkMandatoryParams($params))) {
            return $this->sendError($response, "Missing fields : $missingParams", StatusCode::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->repository->fetchOneBy('email', $params['email']);
            $passwordReset = $this->passwordResetRepository->fetchOneBy('userId', $user->getId());

            if (
                $params['resetCode'] === $passwordReset->getResetCode()
                && $params['id'] === $user->getId()
            ) {
                throw new Exception("ressetCode or userId do not match with database!");
            }

            $apiKey = $this->token->generate($user, $this->settings['settings']['jwt']['secret']);
            $user
                ->setPassword(password_hash($params['newPassword'], PASSWORD_DEFAULT))
                ->setApiKey($apiKey);

            $this->repository->save($user);

            if (!$this->passwordResetRepository->deleteOne($passwordReset->getId())) {
                throw new Exception(
                    "Could not delete the passwordReset entity with userId "
                    . $passwordReset->getUserId()
                );
            }

            return $this->sendSuccess(
                $response,
                "You successfully changed your password."
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->sendError($response, "An error occurred on password change !");
        }
    }
}
