<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Repository\PetRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\DefaultController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PetController
 * @package App\Modules\Pet\Controller
 */
class PetController extends DefaultController
{
    /** @var \App\Modules\Pet\Model\Repository\PetRepository */
    protected $petRepository;

    /**
     * PetController constructor.
     * @param \Framework\Api\Repository\RepositoryInterface $repository
     * @param \App\Modules\Pet\Model\Repository\PetRepository $petRepository
     */
    public function __construct(
        RepositoryInterface $repository,
        PetRepository $petRepository
    ) {
        parent::__construct($repository);
        $this->petRepository = $petRepository;
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        $user = $this->repository->fetchUserByApiKey("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJrZXkiOiJ0ZXN0In0.ge8e6TXC-e7VM4VfrWytaW2YCpP8pIFLRvyj5ycTiF4");



        try {
            if (empty($args['name'])) {
                $pets = $this->repository->fetchPets($user->getId());
                return $response->withJson($pets, 200);
            }

            $name = $args['name'];
            $entity = $this->petRepository->fetchOneBy('name', $name);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 404);
        }


        return $response->withJson($entity, 200);
    }
}
