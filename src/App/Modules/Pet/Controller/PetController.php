<?php

namespace App\Modules\Pet\Controller;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Container\AbstractContainerInjector;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PetController
 * @package App\Modules\Pet\Controller
 */
class PetController extends AbstractContainerInjector
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function all(Request $request, Response $response, $args = []): Response
    {
        $data = $this->container->get('petRepository')->fetchAll();

        return $response->withJson($data, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, Response $response, array $args = []): Response
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
    public function fetchOneByName(Request $request, Response $response, array $args = []): Response
    {
        $data = $this->container->get('petRepository')->fetchOneByName($args['name']);

        return $response->withJson($data, 200);
    }
}
