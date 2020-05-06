<?php

namespace App\Modules\Care\Controller;

use App\Modules\Care\Model\Entity\CareEntity;
use App\Modules\User\Helper\ApiKey;
use App\Modules\User\Model\Repository\UserRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\DefaultController;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class CareController
 * @package App\Modules\Care\Controller
 */
class CareController extends DefaultController
{
    /**
     * @var ApiKey
     */
    protected $apiKeyHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CareController constructor.
     * @param RepositoryInterface $repository
     * @param UserRepository $userRepository
     * @param ApiKey $apiKeyHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        UserRepository $userRepository,
        ApiKey $apiKeyHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($repository, $userRepository);
        $this->apiKeyHelper = $apiKeyHelper;
        $this->logger = $logger;
    }

    /**
     *
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        try {
            $user = $this->apiKeyHelper->getUserByApiKey($request);
            $pet = $this->userRepository->fetchPetBy($user->getId(), $args['petName'], 'name');

            if (empty($args['careId'])) {
                $cares = $this->repository->fetchAllByField('petId', $pet->getId());

                return $this->sendSuccess($response, "Cares of " . $args['petName'] . " pet", $cares);
            }

            $care = $this->repository->fetchOneBy('careId', $args['careId'], "petId=" . $pet->getId());

            return $this->sendSuccess($response, "Care of " . $args['petName'], $care);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when fetching Pet");
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    protected function save(Request $request, Response $response, array $args = []): Response
    {
        try {
            $params = $this->getBodyJsonParams($request);

            $user = $this->apiKeyHelper->getUserByApiKey($request);
            $pet = $this->userRepository->fetchPetBy($user->getId(), $args['petName'], 'name');

            $entityParams = [
                'title' => $params['title'] ?? null,
                'petId' => $pet->getId() ?? null,
                'content' => $params['content'] ?? "",
                'appointmentDate' => $params['appointmentDate'] ?? ""
            ];

            $care = new CareEntity($entityParams);

            $status = StatusCode::HTTP_CREATED;

            if (!empty($args['id'])) {
                $care->setId($args['id']);
                //Just to check if care entity exists, else throws an Exception
                $this->repository->fetchOneBy('careId', $args['careId'], "petId=" . $pet->getId());
                $status = StatusCode::HTTP_OK;
            }

            return $this->sendSuccess($response, 'Care has been saved!', $care, $status);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when Pet save");
        }
    }
}
