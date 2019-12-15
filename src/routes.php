<?php

use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

$app->group('/api', function () {
    $this->group('/pets', function () {
        $this->get('/{name}', 'petController:get');
        $this->get('', 'petController:get');
        $this->post('', 'petController:post');
        $this->put('/{id}', 'petController:put');
        $this->delete('/{id}', 'petController:delete');
    });
});

$app->group('/user', function () {
    $this->post('/login', 'userController:login');
    $this->post('/subscribe', 'userController:subscribe');
    $this->get('/activate/{id}/{activationCode}', 'userController:activate');
});
