<?php

use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

$app->group('/api', function () {
    $this->group('/pets', function () {
        $this->get('/{id}', 'petController:get');
        $this->get('', 'petController:get');
        $this->post('', 'petController:post');
        $this->put('/{id}', 'petController:put');
        $this->delete('/{id}', 'petController:delete');
    });
});

$app->group('/auth', function () {
    $this->get('/generate', function (Request $request, Response $response, array $args) {
        $data = JWT::encode(['key' => 'test'], 'supersecretkeyyoushouldnotcommittogithub');

        return $response->withJson($data, 200);
    });
});
