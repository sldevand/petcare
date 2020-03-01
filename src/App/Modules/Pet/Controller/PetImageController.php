<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Image\Service\ImageManager;
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

    /** @var \App\Modules\Image\Service\ImageManager */
    protected $imageManager;

    /**
     * PetImageController constructor.
     * @param RepositoryInterface $repository
     * @param UserRepository $userRepository
     * @param ApiKey $apiKeyHelper
     * @param LoggerInterface $logger
     * @param ImageManager $imageManager
     */
    public function __construct(
        RepositoryInterface $repository,
        UserRepository $userRepository,
        ApiKey $apiKeyHelper,
        LoggerInterface $logger,
        ImageManager $imageManager
    ) {
        parent::__construct($repository, $userRepository);
        $this->apiKeyHelper = $apiKeyHelper;
        $this->logger = $logger;
        $this->imageManager = $imageManager;
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
            $pet = $this->userRepository->fetchPet($user->getId(), $args['petId']);
            $petImage = $this->repository->fetchOneBy('petId', $pet->getId());

            $imagePath = $petImage->getImage();
            $encodedImage = $this->imageManager->getImageFromPath($imagePath);
            $petImage->setImage($encodedImage);

            return $this->sendSuccess($response, "Fetched Pet Image" . $args['name'], $petImage);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when fetching Pet");
        }
    }
}
