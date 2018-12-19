<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// database
$container['pdo'] = function ($c) {
    $settings = $c->get('settings')['pdo'];
    return \App\Model\Resource\PDOFactory::getSqliteConnexion($settings['file']);
};

// controllers
$container['petController'] = function ($c) {
    return new \App\Controller\PetController($c);
};

// repositories
$container['petRepository'] = function ($c) {
    return new \App\Model\Repository\PetRepository($c->get('pdo'));
};