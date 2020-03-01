<?php

use App\Common\Setup\Installer;
use Framework\Db\Pdo\Query\Builder;
use Framework\Model\Validator\DefaultValidator;
use Framework\Modules\Installed\Model\Repository\InstalledRepository;
use Framework\Resource\PDOFactory;
use Framework\Service\FileManager;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;
use Symfony\Component\Console\Output\ConsoleOutput;

// DIC configuration
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

// validators
$container['defaultValidator'] = function (ContainerInterface $c) {
    return new DefaultValidator();
};

$container['installedRepository'] = function (ContainerInterface $c) {
    return new InstalledRepository(
        $c->get('pdo'),
        $c->get('defaultValidator')
    );
};

// services
$container['fileManager'] = function (ContainerInterface $c) {
    return new FileManager();
};

// InstallDatabase
$container['installer'] = function (ContainerInterface $c) {
    return new Installer(
        $c->get('pdo'),
        $c->get('settings')['pdo']['prod']['db-file'],
        $c->get('consoleOutput'),
        $c->get('queryBuilder'),
        $c->get('installedRepository')
    );
};
