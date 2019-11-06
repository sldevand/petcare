<?php

namespace Framework\Model\Controller;

use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultController
 * @package Framework\Model\Controller
 */
class DefaultController
{
    /** @var RepositoryInterface */
    protected $repository;

    public function __construct(
        RepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        try {
            if (empty($args['id'])) {
                $entities = $this->repository->fetchAll();
                return $response->withJson($entities, 200);
            }

            $id = $args['id'];
            $entity = $this->repository->fetchOne($id);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 404);
        }

        return $response->withJson($entity, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function post(Request $request, Response $response, array $args = []): Response
    {
        $args = $request->getParams();

        if (!empty($args['id'])) {
            return $response->withJson(
                ["errors" => "Cannot create an entity with an id in a POST method"],
                400
            );
        }

        return $this->save($request, $response, $args);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function put(Request $request, Response $response, array $args = []): Response
    {
        if (empty($args['id'])) {
            return $response->withJson(
                ["errors" => "Cannot update an entity with an empty id in a PUT method"],
                400
            );
        }

        $body = $request->getParams();
        $args = array_merge($body, $args);

        return $this->save($request, $response, $args);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args = []): Response
    {
        if (empty($args['id'])) {
            return $response->withJson(
                ["errors" => "Cannot delete an entity with an empty id in a DELETE method"],
                400
            );
        }

        try {
            $id = $args['id'];
            $entity = $this->repository->fetchOne($id);
            $this->repository->deleteOne($id);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 404);
        }

        return $response->withJson($entity, 204);
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
            $entityClass = $this->repository->getEntityClass();
            $entity = new $entityClass($args);
            if (!empty($args['id'])) {
                $entity->setId($args['id']);
            }
            $newEntity = $this->repository->save($entity);
        } catch (Exception $exception) {
            return $response->withJson(["errors" => $exception->getMessage()], 400);
        }

        return $response->withJson($newEntity, 201);
    }
}
