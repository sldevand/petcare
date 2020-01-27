<?php

namespace Framework\Api\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Interface SingleActionControllerInterface
 * @package Framework\Api\Controller
 */
interface SingleActionControllerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function execute(Request $request, Response $response, $args = []): Response;
}
