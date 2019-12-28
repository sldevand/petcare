<?php

namespace App\Modules\User\Controller;

use App\Modules\Token\Helper\Token;
use App\Modules\User\Model\Entity\UserEntity;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class UserController
 * @package App\Modules\User\Controller
 */
class UserController extends AbstractController
{
    /** @var array */
    protected $settings;

    /** @var UserEntity */
    protected $currentUser;

    /** @var Token */
    protected $token;

    /**
     * UserController constructor
     *
     * @param RepositoryInterface $repository
     * @param Token $token
     * @param array $settings
     */
    public function __construct(
        RepositoryInterface $repository,
        Token $token,
        array $settings
    ) {
        parent::__construct($repository);
        $this->settings = $settings;
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, $args = []): Response
    {
        $args = $request->getBody()->getContents();
        $args = json_decode($args, true);

        if (empty($args['email']) || empty($args['password'])) {
            return $this->sendError(
                $response,
                "Cannot login, email or password is missing!",
                StatusCode::HTTP_UNAUTHORIZED
            );
        }

        try {
            $email = $args['email'];
            $password = $args['password'];
            $user = $this->repository->fetchOneBy('email', $args['email']);


            if (!password_verify($password, $user->getPassword())) {
                return $this->sendError(
                    $response,
                    "Wrong Password!",
                    StatusCode::HTTP_UNAUTHORIZED
                );
            }

            if (!$user->getActivated()) {
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
            return $this->sendError(
                $response,
                "User with email $email does not exists"
            );
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function subscribe(Request $request, Response $response, $args = []): Response
    {
        $args = $request->getBody()->getContents();
        $args = json_decode($args, true);

        try {
            $user = new UserEntity($args);

            $apiKey = $this->token->generate($user, $this->settings['settings']['jwt']['secret']);

            $activationCode = bin2hex(random_bytes(24));

            $user
                ->setPassword(password_hash($args['password'], PASSWORD_DEFAULT))
                ->setApiKey($apiKey)
                ->setActivated(0)
                ->setActivationCode($activationCode);

            $this->currentUser = $this->repository->save($user);

            $this->setState('subscribe');

            $return = [
                'email' => $this->currentUser->getEmail(),
                'activated' => $this->currentUser->getActivated()
            ];

            return $this->sendSuccess(
                $response,
                "An activation link was sent by email, please click the link to activate your account",
                $return
            );
        } catch (Exception $e) {
            return $this->sendError($response, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function activate(Request $request, Response $response, $args = []): Response
    {
        if (empty($args['id']) || empty($args['activationCode'])) {
            return $this->sendError(
                $response,
                "Cannot activate user, id or activation Code missing",
                StatusCode::HTTP_OK
            );
        }

        try {
            $user = $this->repository->fetchOne($args['id']);

            if (!empty($user->getActivated())) {
                return $this->sendError(
                    $response,
                    "User has already been activated",
                    StatusCode::HTTP_OK
                );
            }

            if ($user->getActivationCode() !== $args['activationCode']) {
                return $this->sendError(
                    $response,
                    "Activation code from email is different from activation code in database",
                    StatusCode::HTTP_OK
                );
            }

            $user->setActivated(1);
            $activatedUser = $this->repository->save($user);

            $return = [
                'email' => $activatedUser->getEmail(),
                'activated' => $activatedUser->getActivated()
            ];

            return $this->sendSuccess(
                $response,
                "Your account is activated, Welcome !",
                $return
            );
        } catch (Exception $e) {
            return $this->sendError($response, $e->getMessage());
        }
    }

    /**
     * @return UserEntity
     */
    public function getCurrentUser(): UserEntity
    {
        return $this->currentUser;
    }
}
