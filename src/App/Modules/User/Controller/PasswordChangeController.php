<?php

namespace App\Modules\User\Controller;

use App\Modules\PasswordReset\Model\Repository\PasswordResetRepository;
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
    protected $mandatoryParams = ['id', 'resetCode','email', 'newPassword'];

    /** @var PasswordResetRepository */
    protected $passwordResetRepository;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * PasswordChangeController constructor.
     * @param RepositoryInterface $repository
     * @param PasswordResetRepository $passwordResetRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        PasswordResetRepository $passwordResetRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($repository);
        $this->passwordResetRepository = $passwordResetRepository;
        $this->logger = $logger;
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

            //TODO Check if id and resetCode matches with db

            //TODO change user password and apiKey

            //TODO delete passwordReset row with user_id

            return $this->sendSuccess(
                $response,
                "You successfully changed your password."
            );
        } catch (RepositoryException $e) {
            $argEmail = $params['email'];
            return $this->sendError($response, "This user with email $argEmail doesn't exists !");
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->sendError($response, "An error occurred on password change !");
        }
    }
}
