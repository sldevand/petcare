<?php

namespace App\Modules\User\Controller;

use Framework\Controller\AbstractController;
use App\Modules\User\Model\Entity\UserEntity;
use Slim\Http\Request;
use Slim\Http\Response;
use Exception;
use Firebase\JWT\JWT;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Model\Validator\DefaultValidator;

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

    /**
     * UserController constructor
     *
     * @param RepositoryInterface $repository
     * @param array $settings
     */
    public function __construct(
        RepositoryInterface $repository,
        array $settings
    ) {
        parent::__construct($repository);
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, $args = []): Response
    {
        $args = $request->getParams();

        if (empty($args['email']) || empty($args['password'])) {
            return $response->withJson(
                ["errors" => "Cannot login, email or password is missing!"],
                401
            );
        }

        try {
            $email = $args['email'];
            $password = $args['password'];
            $user = $this->repository->fetchOneBy('email', $args['email']);

            if (!password_verify($password, $user->getPassword())) {
                return $response->withJson(
                    ["errors" => "Wrong password !"],
                    401
                );
            }

            if (!$user->getActivated()) {
                return $response->withJson(
                    ["errors" => "User is not activated, please click the link in your email to activate the account"],
                    401
                );
            }

            $return = [
                'email' => $user->getEmail(),
                'apiKey' => $user->getApiKey()
            ];

            return $response->withJson($return, 200);
        } catch (Exception $e) {
            return $response->withJson(
                ["errors" => "User with email $email does not exists"],
                404
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
        $args = $request->getParams();
        try {
            $user = new UserEntity($args);

            $apiKey = JWT::encode(
                [
                    'lastName' => $user->getLastName(),
                    'email' => $user->getEmail()
                ],
                $this->settings['settings']['jwt']['secret'],
                "HS256"
            );

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
                'apiKey' => $this->currentUser->getApiKey()
            ];

            return $response->withJson($return, 201);
        } catch (Exception $e) {
            return $response->withJson(
                ["errors" => $e->getMessage()],
                404
            );
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
            return $response->withJson(
                ["errors" => "Cannot activate user, id or activation Code missing"],
                401
            );
        }

        try {
            $user = $this->repository->fetchOne($args['id']);

            if (!empty($user->getActivated())) {
                return $response->withJson(
                    ["errors" => "User has already been activated"],
                    304
                );
            }

            if ($user->getActivationCode() !== $args['activationCode']) {
                return $response->withJson(
                    ["errors" => "activation code from email is different from activation code in database"],
                    304
                );
            }

            $user->setActivated(1);
            $activatedUser = $this->repository->save($user);

            $return = [
                'email' => $activatedUser->getEmail(),
                'activated' => $activatedUser->getActivated()
            ];

            return $response->withJson($return, 200);
        } catch (Exception $e) {
            return $response->withJson(
                ["errors" => $e->getMessage()],
                404
            );
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
