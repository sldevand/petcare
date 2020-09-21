<?php

namespace App\Modules\User\Controller;

use App\Modules\Activation\Model\Entity\ActivationEntity;
use App\Modules\Activation\Model\Repository\ActivationRepository;
use App\Modules\Token\Helper\Token;
use App\Modules\User\Model\Entity\UserEntity;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use PDOException;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SubscribeController
 * @package App\Modules\User\Controller
 */
class SubscribeController extends AbstractController
{
    /** @var array */
    protected $settings;

    /** @var UserEntity */
    protected $currentUser;

    /** @var Token */
    protected $token;

    /** @var ActivationRepository */
    protected $activationRepository;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * SubscribeController constructor
     *
     * @param RepositoryInterface $repository
     * @param Token $token
     * @param ActivationRepository $activationRepository
     * @param LoggerInterface $logger
     * @param array $settings
     */
    public function __construct(
        RepositoryInterface $repository,
        Token $token,
        ActivationRepository $activationRepository,
        LoggerInterface $logger,
        array $settings
    ) {
        parent::__construct($repository);
        $this->settings = $settings;
        $this->token = $token;
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

        try {
            $user = new UserEntity($params);

            $apiKey = $this->token->generate($user, $this->settings['settings']['jwt']['secret']);

            $activationCode = bin2hex(random_bytes(24));

            $user->setPassword(password_hash($params['password'], PASSWORD_DEFAULT))
                ->setApiKey($apiKey);

            $this->currentUser = $this->repository->save($user);

            $this->activationRepository->save(
                new ActivationEntity(
                    [
                        'userId'         => $this->currentUser->getId(),
                        'activated'      => "0",
                        'mailSent'       => "0",
                        'activationCode' => $activationCode
                    ]
                )
            );

            $this->setState('subscribe');

            $activation = $this->activationRepository->fetchOneBy('userId', $this->currentUser->getId());

            $return = [
                'email' => $this->currentUser->getEmail(),
                'activated' => $activation->getActivated()
            ];

            return $this->sendSuccess(
                $response,
                "An activation link was sent by email, please click the link to activate your account",
                $return
            );
        } catch (PDOException $e) {
            $this->logger->alert($e->getMessage());
            return $this->sendError($response, $this->getErrorMessage($e));
        } catch (Exception $e) {
            $this->logger->alert($e->getMessage());
            if (!empty($this->currentUser)) {
                $this->repository->deleteOne($this->currentUser->getId());
            }

            return $this->sendError($response, "An error occurred on subscription");
        }
    }

    /**
     * @return UserEntity
     */
    public function getCurrentUser(): UserEntity
    {
        return $this->currentUser;
    }

    /**
     * @param Exception $exception
     * @return string
     */
    protected function getErrorMessage(Exception $exception): string
    {
        switch ($exception->getCode()) {
            case '23000':
                return 'User already exists';
                break;
            default:
                return 'An error occurred';
        }
    }
}
