<?php

use Slim\Middleware\JwtAuthentication;

$app->add(new JwtAuthentication(
    [
        "attribute" => "decoded_token_data",
        "secret" => $settings['settings']['jwt']['secret'],
        "algorithm" => ["HS256"],
        "secure" => false,
        "path" => ["/api"]
    ]
));

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
