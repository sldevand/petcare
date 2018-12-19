<?php

namespace App\Controller;

use Lib\Container\AbstractContainerInjector;
use App\Model\Entity\PetEntity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PetController
 * @package App\Controller
 */
class PetController extends AbstractContainerInjector
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function all(Request $request, Response $response, $args = [])
    {
        $data = $this->container->get('petRepository')->fetchAll();

        return $response->withJson($data, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function create(Request $request, Response $response, $args = [])
    {
        $args = $request->getParams();
        $entity = new PetEntity($args);

        if (!$this->container->get('petRepository')->create($entity)) {
            return $response->withJson(["message" => "entity not created"], 204);
        }

        return $response->withJson($entity, 201);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findOneByName(Request $request, Response $response, $args = [])
    {
        $data = $this->container->get('petRepository')->findOneByName($args['name']);

        return $response->withJson($data, 200);
    }
}
