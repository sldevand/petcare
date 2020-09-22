<?php

use Symfony\Component\Dotenv\Dotenv;

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

$dotEnv = new Dotenv();
$dotEnv->load(__DIR__ . '/.env');

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../var/log/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // PDO settings
        'pdo' => [
            'prod' => [
                'db-file' =>  $_ENV['PDO_PROD_FILE']
            ],
            'test' => [
                'db-file' => $_ENV['PDO_TEST_FILE']
            ]
        ],

        //JWT settings
        'jwt' => [
            'secret' =>  $_ENV['JWT_SECRET']
        ],

        'assets' => [
            'images' => $_ENV['IMAGES_PATH']
        ]
    ],
];
