<?php

use Slim\App;

if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

//error_reporting(-1);
//ini_set('display_errors', true);

require __DIR__ . '/../src/bootstrap.php';
require VENDOR_DIR . '/autoload.php';
session_start();
$settings = require SRC_DIR . '/settings.php';
$container = new \Slim\Container($settings);
require SRC_DIR . '/dependencies.php';
$app = new App($container);
require SRC_DIR . '/middleware.php';
require SRC_DIR . '/routes.php';

$app->run();
