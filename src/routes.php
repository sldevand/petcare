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
    $this->group('/cares', function () {
        $this->get('/{petName}/{id}', 'careController:get');
        $this->get('/{petName}', 'careController:get');
        $this->post('/{petName}', 'careController:post');
        $this->put('/{petName}/{id}', 'careController:put');
        $this->delete('/{petName}/{id}', 'careController:delete');
    });
    $this->group('/user', function () {
        $this->get('', 'userApiController:get');
    });
});

$app->group('/user', function () {
    $this->post('/login', 'userLoginController:execute');
    $this->post('/subscribe', 'userSubscribeController:execute');
    $this->get('/activate/{id}/{activationCode}', 'userActivateController:execute');
    $this->post('/passwordReset', 'userPasswordResetController:execute');
    $this->post('/passwordChange/{id}/{resetCode}', 'userPasswordChangeController:execute');
});
