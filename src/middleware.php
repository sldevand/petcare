<?php

use Slim\Middleware\JwtAuthentication;

$app->add(new JwtAuthentication(
    [
        "attribute" => "decoded_token_data",
        "secret" => "supersecretkeyyoushouldnotcommittogithub",
        "algorithm" => ["HS256"],
        "secure" => false,
        "path" => ["/api"]
    ]
));
