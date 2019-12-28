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

$app->add(new Tuupola\Middleware\CorsMiddleware());
