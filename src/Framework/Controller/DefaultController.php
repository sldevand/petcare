<?php

namespace Framework\Controller;

use App\Modules\User\Model\Repository\UserRepository;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Repository\RepositoryInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class DefaultController
 * @package Framework\Controller
 */
class DefaultController extends AbstractController
{
    /** @var \App\Modules\User\Model\Repository\UserRepository */
    protected $userRepository;

    /**
     * DefaultController constructor.
     * @param \Framework\Api\Repository\RepositoryInterface $repository
     * @param \App\Modules\User\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        RepositoryInterface $repository,
        UserRepository $userRepository
    ) {
        parent::__construct($repository);
        $this->userRepository = $userRepository;
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
                return $this->sendSuccess($response, 'Entities successfully fetched!', $entities);
            }

            $id = $args['id'];
            $entity = $this->repository->fetchOne($id);
        } catch (Exception $exception) {
            return $this->sendError($response, 'Error while fetching entity!');
        }

        return $this->sendSuccess($response, 'Entity successfully fetched!', $entity);
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
            return $this->sendError(
                $response,
                "Cannot create an entity with an id in a POST method",
                StatusCode::HTTP_NOT_ACCEPTABLE
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
            return $this->sendError(
                $response,
                "Cannot update an entity with an empty id in a PUT method",
                StatusCode::HTTP_NOT_ACCEPTABLE
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
            return $this->sendError(
                $response,
                "Cannot delete an entity with an empty id in a DELETE method",
                StatusCode::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $id = $args['id'];
            $entity = $this->repository->fetchOne($id);
            $this->repository->deleteOne($id);
        } catch (Exception $exception) {
            return $this->sendError(
                $response,
                "An error occurred while deleting the entity",
                StatusCode::HTTP_NOT_ACCEPTABLE
            );
        }

        return $this->sendSuccess($response, "Entity successfully deleted", $entity);
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
            return $this->sendError($response, "An error occurred while saving the entity");
        }

        return $this->sendSuccess(
            $response,
            'Entity successfully saved!',
            $newEntity,
            StatusCode::HTTP_CREATED
        );
    }

    /**
     * @param \Slim\Http\Request $request
     * @return string
     * @throws \Exception
     */
    protected function getApiKey(Request $request): string
    {
        $authHeaderArr = $request->getHeader('Authorization');
        if (empty($authHeaderArr) && count($authHeaderArr) !== 1) {
            throw new Exception('No authorization header found');
        }

        $authorizationHeaderArr = explode(' ', $authHeaderArr[0]);

        if (empty($authorizationHeaderArr) && count($authorizationHeaderArr) !== 2) {
            throw new Exception('No authorization header found');
        }

        return $authorizationHeaderArr[1];
    }

    /**
     * @param \Slim\Http\Request $request
     * @return \Framework\Api\Entity\EntityInterface
     * @throws \Exception
     */
    protected function getUserByApiKey(Request $request): EntityInterface
    {
        $apiKey = $this->getApiKey($request);
        return $this->userRepository->fetchByApiKey($apiKey);
    }
}
