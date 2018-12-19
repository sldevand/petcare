<?php

use Firebase\JWT\JWT;

$app->add(new Slim\Middleware\JwtAuthentication(
    [
        "attribute" => "decoded_token_data",
        "secret" => "supersecretkeyyoushouldnotcommittogithub",
        "algorithm" => ["HS256"],
        "secure" => false,
        "path" => ["/api"]
    ]
));

//var_dump(JWT::encode(["J'apprécie les fruits au sirop"],'supersecretkeyyoushouldnotcommittogithub'));