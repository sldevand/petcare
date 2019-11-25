<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Controller\DefaultController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PetController
 * @package App\Modules\Pet\Controller
 */
class PetController extends DefaultController
{
    /** @var \App\Modules\User\Model\Repository\UserRepository */
    protected $userRepository;

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        try {
            $user = $this->getUserByApiKey($request);

            if (empty($args['name'])) {
                $pets = $this->userRepository->fetchPets($user->getId());
                return $response->withJson($pets, 200);
            }

            $pet = $this->repository->fetchOneBy('name', $args['name']);

            return $response->withJson($pet, 200);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 404);
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
            $user = $this->getUserByApiKey($request);


            $entityParams = [
                'name' => $request->getParam('name'),
                'specy' => $request->getParam('specy'),
                'dob' => $request->getParam('dob')
            ];

            if (!empty($request->getParam('id'))) {
                $entityParams['id'] = $request->getParam('id');
            }

            $pet = new PetEntity($entityParams);

            $user->addPet($pet);

            $user = $this->userRepository->save($user);


            var_dump($user);
            die;
            $newPet = $user->getPet($pet->getName());

            return $response->withJson($newPet, 201);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 400);
        }
    }
}
