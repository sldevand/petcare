<?php

use App\Model\Entity\PetEntity;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

$petRepo = new \App\Model\Repository\PetRepository($container->get('pdo'));

$app->group('/api', function () {
    $this->group('/pets', function () {
        $this->get('', 'petController:all');
        $this->post('/new', 'petController:create');
        $this->get('/{name}', 'petController:findOneByName');
    });
});

$app->group('/auth', function () {
    $this->get('/generate', function (Request $request, Response $response, array $args) {
        $data = JWT::encode(["sirop" => "J'apprécie les fruits au sirop"], 'supersecretkeyyoushouldnotcommittogithub');

        return $response->withJson($data, 200);
    });
});

