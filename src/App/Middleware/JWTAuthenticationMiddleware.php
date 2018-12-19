<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class JWTAuthenticationMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {

        new Tuupola\Middleware\JwtAuthentication([
        "secret" => "supersecretkeyyoushouldnotcommittogithub"
    ]);
    }
}