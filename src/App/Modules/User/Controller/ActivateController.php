<?php

namespace App\Modules\User\Controller;

use App\Modules\Activation\Model\Repository\NotificationRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class ActivateController
 * @package App\Modules\User\Controller
 */
class ActivateController extends AbstractController
{
    /** @var NotificationRepository */
    protected $activationRepository;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * ActivateController constructor
     * @param RepositoryInterface $repository
     * @param NotificationRepository $activationRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        NotificationRepository $activationRepository,
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
        if (empty($args['id']) || empty($args['activationCode'])) {
            return $this->sendError(
                $response,
                "Cannot activate user, id or activation Code missing",
                StatusCode::HTTP_OK
            );
        }

        try {
            $user = $this->repository->fetchOne($args['id']);
            $activation = $this->activationRepository->fetchOneBy('userId', $user->getId());

            if (!empty($activation->getActivated())) {
                return $this->sendError(
                    $response,
                    "User has already been activated",
                    StatusCode::HTTP_OK
                );
            }

            if ($activation->getActivationCode() !== $args['activationCode']) {
                return $this->sendError(
                    $response,
                    "Activation code from email is different from activation code in database",
                    StatusCode::HTTP_OK
                );
            }

            $activation->setActivated(1);
            $savedActivation = $this->activationRepository->save($activation);

            $return = [
                'email' => $user->getEmail(),
                'activated' => $savedActivation->getActivated()
            ];

            return $this->sendSuccess(
                $response,
                "Your account is activated, Welcome !",
                $return
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->sendError($response, "An error occurred on activation");
        }
    }
}
