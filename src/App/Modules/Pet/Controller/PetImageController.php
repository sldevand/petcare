<?php

namespace App\Modules\Pet\Controller;

use App\Modules\User\Helper\ApiKey;
use App\Modules\User\Model\Repository\UserRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\DefaultController;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PetImageController
 * @package App\Modules\Pet\Controller
 */
class PetImageController extends DefaultController
{
    /** @var \App\Modules\User\Model\Repository\UserRepository */
    protected $userRepository;

    /** @var \App\Modules\User\Helper\ApiKey */
    protected $apiKeyHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * PetImageController constructor.
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

            if (empty($args['petId'])) {
                $pets = $this->userRepository->fetchPets($user->getId());

                //TODO check if petImages belong to user pets

                $petImages = $this->repository->fetchAll();

                return $this->sendSuccess($response, "Fetched Pet Image" . $args['name'], $petImages);
            }
            $petImage = $this->repository->fetchOneBy('petId', $args['petId']);

            return $this->sendSuccess($response, "Fetched Pet Image" . $args['name'], $petImage);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when fetching Pet");
        }
    }
}
