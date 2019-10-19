<?php

use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Modules\Pet\Model\Repository\PetRepository;

$container = $app->getContainer();

$petRepo = new PetRepository($container->get('pdo'));

$app->group('/api', function () {
    $this->group('/pets', function () {
        $this->get('', 'petController:all');
        $this->post('/new', 'petController:create');
        $this->get('/{name}', 'petController:findOneByName');
    });
});

$app->group('/auth', function () {
    $this->get('/generate', function (Request $request, Response $response, array $args) {
        $data = JWT::encode(["key" => "test"], 'supersecretkeyyoushouldnotcommittogithub');

        return $response->withJson($data, 200);
    });
});
