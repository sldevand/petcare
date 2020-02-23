<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Entity\PetEntity;
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
 * Class PetController
 * @package App\Modules\Pet\Controller
 */
class PetController extends DefaultController
{
    /** @var \App\Modules\User\Model\Repository\UserRepository */
    protected $userRepository;

    /** @var \App\Modules\User\Helper\ApiKey */
    protected $apiKeyHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * PetController constructor.
     * @param \Framework\Api\Repository\RepositoryInterface $repository
     * @param \App\Modules\User\Model\Repository\UserRepository $userRepository
     * @param \App\Modules\User\Helper\ApiKey $apiKeyHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        UserRepository $userRepository,
        ApiKey $apiKeyHelper,
        LoggerInterface $logger
    )
    {
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

            if (empty($args['name'])) {
                $pets = $this->userRepository->fetchPets($user->getId());
                return $this->sendSuccess($response, 'List of pets', $pets);
            }

            $pet = $this->repository->fetchOneBy('name', $args['name']);

            return $this->sendSuccess($response, "Informations on " . $args['name'], $pet);
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

            $entityParams = [
                'name'  => $params['name'] ?? "",
                'specy' => $params['specy'] ?? "",
                'dob'   => $params['dob'] ?? ""
            ];

            $pet = new PetEntity($entityParams);

            if (!empty($args['id'])) {
                $pet->setId($args['id']);
                $this->userRepository->fetchPet($user->getId(), $pet->getId());
            }

            $newPet = $this->userRepository->savePet($user, $pet);

            return $this->sendSuccess($response, 'Pet has been saved!', $newPet, StatusCode::HTTP_CREATED);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when Pet save");
        }
    }
}
