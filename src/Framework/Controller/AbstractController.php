<?php

namespace Framework\Controller;

use Framework\Api\Repository\RepositoryInterface;
use Framework\Observer\Subject;
use Slim\Http\Response;
use Slim\Http\StatusCode;

/**
 * Class AbstractController
 * @package Framework\Controller
 */
abstract class AbstractController extends Subject
{
    /** @var RepositoryInterface */
    protected $repository;

    /**
     * AbstractController constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(
        RepositoryInterface $repository
    ) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @param Response $response
     * @param string $error
     * @param int $status
     * @return Response
     */
    protected function sendError(Response $response, string $error, $status = StatusCode::HTTP_NOT_FOUND): Response
    {
        return $response->withJson(
            [
                "status" => 0,
                "errors" => $error
            ],
            $status
        );
    }

    /**
     * @param Response $response
     * @param string $message
     * @param array $data
     * @param int $status
     * @return Response
     */
    protected function sendSuccess(Response $response, string $message, array $data = [], $status = StatusCode::HTTP_OK): Response
    {
        $responseArray = array_merge(
            [
                "status" => 1,
                'message' => $message
            ],
            $data
        );

        return $response->withJson(
            $responseArray,
            $status
        );
    }
}
