<?php

require __DIR__ . '/../src/bootstrap.php';
require VENDOR_DIR . '/autoload.php';

use Slim\App;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$settings = require SRC_DIR . '/settings.php';
$container = new \Slim\Container($settings);
require SRC_DIR . '/dependencies.php';
$app = new App($container);

$crontab = [
    'purge_never_activated_users' => [
        'expression' => '* * * * *',
        'executor' => '\App\Modules\User\Cron\DeleteNeverActivatedUsersExecutor',
        'args' => ['app' => $app]
    ]
];

$launcher = new \Sldevand\Cron\Launcher($crontab);
$launcher->launch();
