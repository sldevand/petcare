<?php

namespace App\Modules\User\Controller;

use App\Modules\Activation\Model\Repository\ActivationRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class LoginController
 * @package App\Modules\User\Controller
 */
class LoginController extends AbstractController
{
    /** @var ActivationRepository */
    protected $activationRepository;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * LoginController constructor
     *
     * @param RepositoryInterface $repository
     * @param ActivationRepository $activationRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        ActivationRepository $activationRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($repository);
        $this->activationRepository = $activationRepository;
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

        if (empty($params['email']) || empty($params['password'])) {
            return $this->sendError(
                $response,
                "Cannot login, email or password is missing!",
                StatusCode::HTTP_UNAUTHORIZED
            );
        }

        try {
            $email = $params['email'];
            $password = $params['password'];

            $user = $this->repository->fetchOneBy('email', $params['email']);
            if (!password_verify($password, $user->getPassword())) {
                return $this->sendError(
                    $response,
                    "Wrong Password!",
                    StatusCode::HTTP_UNAUTHORIZED
                );
            }

            $activation = $this->activationRepository->fetchOneBy('userId', $user->getId());

            if (!$activation->getActivated()) {
                return $this->sendError(
                    $response,
                    "User is not activated, please click the link in your email to activate the account!",
                    StatusCode::HTTP_UNAUTHORIZED
                );
            }

            $return = [
                'email' => $user->getEmail(),
                'apiKey' => $user->getApiKey()
            ];

            return $this->sendSuccess($response, "You have successfully logged in!", $return);
        } catch (Exception $e) {
            $this->logger->alert($e->getMessage());
            return $this->sendError(
                $response,
                "User with email $email does not exists"
            );
        }
    }
}
