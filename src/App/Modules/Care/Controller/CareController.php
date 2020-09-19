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

            if (empty($args['id'])) {
                $options = ['orderBy' => 'appointmentDate', 'direction' => 'desc'];
                $cares = $this->repository->fetchAllByField('petId', $pet->getId(), $options);

                return $this->sendSuccess($response, "List of Cares", $cares);
            }

            $care = $this->repository->fetchOneBy('id', $args['id'], "petId=" . $pet->getId());
            $title = $care->getTitle();
            $petName = $pet->getName();

            return $this->sendSuccess($response, "Care $title for $petName", $care);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when fetching Care");
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
                'title' => $params['title'] ?? $args['title'] ?? null,
                'petId' => $pet->getId() ?? null,
                'content' => $params['content'] ?? $args['content'] ?? '',
                'appointmentDate' => $params['appointmentDate'] ?? $args['appointmentDate'] ?? ''
            ];

            foreach ($entityParams as $key => $entityParam) {
                if (is_null($entityParam)) {
                    return $this->sendError($response, "No $key has been sent");
                }
            }

            $care = new CareEntity($entityParams);

            $status = StatusCode::HTTP_CREATED;

            if (!empty($args['id'])) {
                $care->setId(intval($args['id']));
                //Just to check if care entity exists, else throws an Exception
                $this->repository->fetchOneBy('id', $args['id'], "petId=" . $pet->getId());
                $status = StatusCode::HTTP_OK;
            }

            $savedCare = $this->repository->save($care);

            $title = $savedCare->getTitle();

            return $this->sendSuccess($response, "Care $title has been saved!", $savedCare, $status);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when Care save");
        }
    }
}
