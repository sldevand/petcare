<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Entity\PetImageEntity;
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
                'name' => $params['name'] ?? "",
                'specy' => $params['specy'] ?? "",
                'dob' => $params['dob'] ?? ""
            ];

            $pet = new PetEntity($entityParams);

            if (!empty($args['id'])) {
                $pet->setId($args['id']);
                $this->userRepository->fetchPet($user->getId(), $pet->getId());
            }


            if (!empty($params['image'])) {
                $file = $this->resolveImagePath($user->getId(), $params['name']);
                $pet->setImage(new PetImageEntity(['image' => $file]));
            }

            $newPet = $this->userRepository->savePet($user, $pet);

            if (!empty($params['image'])) {
                $this->makeDirectory(IMAGES_DIR . '/' . $user->getId());
                $this->makeDirectory(IMAGES_DIR . '/' . $user->getId() . '/pets');
                $this->generateImage($params['image'], $file);
            }

            return $this->sendSuccess($response, 'Pet has been saved!', $newPet, StatusCode::HTTP_CREATED);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
            return $this->sendError($response, "An error occurred when Pet save");
        }
    }

    /**
     * @param string $id
     * @param string $fileName
     * @return string
     */
    public function resolveImagePath(string $id, string $fileName): string
    {
        return IMAGES_DIR . '/' . $id . '/pets/' . $fileName . '.png';
    }

    /**
     * @param string $dir
     * @throws Exception
     */
    public function makeDirectory(string $dir)
    {
        if (file_exists($dir)) {
            return;
        }

        if (!mkdir($dir)) {
            throw new \Exception('Could not mkdir : ' . $dir);
        }
    }

    /**
     * @param string $img
     * @param string $file
     * @throws Exception
     */
    public function generateImage(string $img, string $file)
    {
        error_reporting(-1);
        ini_set('display_errors', true);


        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        if (!file_put_contents($file, $image_base64)) {
            throw new \Exception('Could not file_put_contents file : ' . $file);
        }
    }
}
