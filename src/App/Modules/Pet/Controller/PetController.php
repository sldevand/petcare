<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Controller\DefaultController;
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
                return $this->sendSuccess($response, 'List of pets', $pets);
            }

            $pet = $this->repository->fetchOneBy('name', $args['name']);

            return $this->sendSuccess($response, "Informations on " . $args['name'], $pet);
        } catch (Exception $exception) {
            return $this->sendError($response, $exception->getMessage());
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

            $pet = new PetEntity($entityParams);

            if (!empty($args['id'])) {
                $pet->setId($args['id']);
                $this->userRepository->fetchPet($user->getId(), $pet->getId());
            }

            $newPet = $this->userRepository->savePet($user, $pet);

            return $this->sendSuccess($response, 'Pet has been saved!', $newPet, StatusCode::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->sendError($response, "An error occurred when Pet save");
        }
    }
}
