<?php

use Slim\Http\Request;
use Slim\Http\Response;

$petRepo = new \App\Model\Repository\PetRepository($container->get('pdo'));

$app->get('/api/pets', function (Request $request, Response $response, array $args) use ($petRepo){

    $data = $petRepo->fetchAll();

    return $response->withJson($data,200);
});


$app->get('/api/pets/{name}', function (Request $request, Response $response, array $args) use ($petRepo){
    $data = $petRepo->findOneByName($args['name']);

    return $response->withJson($data,200);
});
