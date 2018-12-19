<?php

use App\Model\Entity\PetEntity;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

$petRepo = new \App\Model\Repository\PetRepository($container->get('pdo'));

$app->get('/api/pets', function (Request $request, Response $response, array $args) use ($petRepo){

    $data = $petRepo->fetchAll();

    return $response->withJson($data,200);
});

$app->post('/api/pets/new', function (Request $request, Response $response, array $args) use ($petRepo){
    $params = $request->getParams();
    $entity = new PetEntity($params['name'],$params['age'],$params['specy']);

    if(!$petRepo->create($entity)){
        return $response->withJson(["message" => "entity not created"],204);
    }

    return $response->withJson($entity,201);
});

$app->get('/api/pets/{name}', function (Request $request, Response $response, array $args) use ($petRepo){
    $data = $petRepo->findOneByName($args['name']);

    return $response->withJson($data,200);
});

$app->get('/authenticate',function(Request $request,Response $response, array $args) {
    $data = JWT::encode(["J'apprécie les fruits au sirop"], 'supersecretkeyyoushouldnotcommittogithub');

    return $response->withJson($data, 200);
});