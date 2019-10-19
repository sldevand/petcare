<?php

use App\Common\Setup\Installer;
use App\Modules\Pet\Controller\PetController;
use App\Modules\Pet\Model\Repository\PetRepository;
use Framework\Resource\PDOFactory;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;
use Framework\Model\Validator\DefaultValidator;
use Symfony\Component\Console\Output\ConsoleOutput;

// DIC configuration
/** @var ContainerInterface $container */
$container = $app->getContainer();

// view renderer
$container['renderer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['renderer'];

    return new PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// database
$container['pdo'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['pdo']['prod'];

    return PDOFactory::getSqliteConnexion($settings['db-file']);
};

// InstallDatabase
$container['installer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['pdo']['prod'];
    $output = new ConsoleOutput();
    return new Installer($c->get('pdo'), $settings['install-file'], $output);
};

// controllers
$container['petController'] = function (ContainerInterface $c) {
    return new PetController($c);
};

// repositories
$container['petRepository'] = function (ContainerInterface $c) {
    return new PetRepository($c->get('pdo'), new DefaultValidator());
};
