<?php

use App\Common\Setup\Installer;
use App\Modules\Pet\Controller\PetController;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use Framework\Db\Pdo\Query\Builder;
use Framework\Model\Validator\DefaultValidator;
use Framework\Resource\PDOFactory;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;
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

$container['consoleOutput'] = function (ContainerInterface $c) {
    return new ConsoleOutput();
};

$container['queryBuilder'] = function (ContainerInterface $c) {
    return new Builder();
};

// InstallDatabase
$container['installer'] = function (ContainerInterface $c) {
    return new Installer(
        $c->get('pdo'),
        $c->get('settings')['pdo']['prod']['install-file'],
        $c->get('consoleOutput'),
        $c->get('queryBuilder')
    );
};

// controllers
$container['petController'] = function (ContainerInterface $c) {
    return new PetController($c);
};

//validators
$container['defaultValidator'] = function (ContainerInterface $c) {
    return new DefaultValidator();
};

// repositories
$container['petImageRepository'] = function (ContainerInterface $c) {
    return new PetImageRepository($c->get('pdo'), $c['defaultValidator']);
};

$container['petRepository'] = function (ContainerInterface $c) {
    return new PetRepository($c->get('pdo'), $c['defaultValidator']);
};
