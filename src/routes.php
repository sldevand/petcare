<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/api/pets', function (Request $request, Response $response, array $args) {
    $data = [
        1 => [
            'name' => 'brownie',
            'type' => 'cat',
            'hair' => 'brown'
        ]
    ];

    // Render index view
    return $response->withJson($data);
});


$app->get('/api/pets/{name}', function (Request $request, Response $response, array $args) {
    $data = [
        1 => [
            'name' => $args['name'],
            'type' => 'cat',
            'hair' => 'brown'
        ]
    ];

    // Render index view
    return $response->withJson($data);
});



