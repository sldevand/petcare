<?php

use App\Controller\PetController;
use App\Model\Repository\PetRepository;
use App\Setup\InstallDatabase;
use Lib\Resource\PDOFactory;
use Slim\Views\PhpRenderer;

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new PhpRenderer($settings['template_path']);
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
    $settings = $c->get('settings')['pdo']['prod'];
    return PDOFactory::getSqliteConnexion($settings['db-file']);
};

// InstallDatabase
$container['installDatabase'] = function ($c) {
    $settings = $c->get('settings')['pdo']['prod'];
    return new InstallDatabase($c->get('pdo'),$settings['install-file']);
};

// controllers
$container['petController'] = function ($c) {
    return new PetController($c);
};

// repositories
$container['petRepository'] = function ($c) {
    return new PetRepository($c->get('pdo'));
};