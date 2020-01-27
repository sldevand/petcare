<?php

$container = $app->getContainer();

$app->group('/api', function () {
    $this->group('/pets', function () {
        $this->get('/{name}', 'petController:get');
        $this->get('', 'petController:get');
        $this->post('', 'petController:post');
        $this->put('/{id}', 'petController:put');
        $this->delete('/{id}', 'petController:delete');
    });
    $this->group('/user', function () {
        $this->get('', 'userApiController:get');
    });
});

$app->group('/user', function () {
    $this->post('/login', 'userLoginController:execute');
    $this->post('/subscribe', 'userSubscribeController:execute');
    $this->get('/activate/{id}/{activationCode}', 'userActivateController:execute');
});
