<?php

namespace Tests\Integration\Framework;

use App\Common\Setup\Installer;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use Framework\Db\Pdo\Query\Builder;
use Framework\Model\Validator\DefaultValidator;
use Framework\Resource\PDOFactory;
use Psr\Container\ContainerInterface;
use Slim\App;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class BaseTestFramework
 * @package Tests\Integration
 */
class BaseTestFramework
{
    /**
     * @return App
     */
    public static function generateApp(): App
    {
        require_once __DIR__ . '/../../../src/bootstrap.php';
        $settings = require __DIR__ . '/../../../src/settings.php';
        $app = new App($settings);
        $container = $app->getContainer();

        $container['pdoTest'] = function (ContainerInterface $container) {
            $settings = $container->get('settings')['pdo']['test'];
            unlink($settings['db-file']);
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };
        $container['installerTest'] = function (ContainerInterface $container) {
            $settings = $container->get('settings')['pdo']['test'];
            $output = new ConsoleOutput();
            $queryBuilder = new Builder();
            return new Installer($container->get('pdoTest'), $settings['install-file'], $output, $queryBuilder);
        };

        $container['defaultValidator'] = function (ContainerInterface $container) {
            return new DefaultValidator();
        };

        $container['petImageRepository'] = function (ContainerInterface $container) {
            $pdo = $container->get('pdoTest');
            return new PetImageRepository($pdo, $container->get('defaultValidator'));
        };

        $container['petRepository'] = function (ContainerInterface $container) {
            $pdo = $container->get('pdoTest');
            return new PetRepository($pdo, $container->get('defaultValidator'), $container->get('petImageRepository'));
        };

        $container['installerTest'] = function (ContainerInterface $container) {
            $settings = $container->get('settings')['pdo']['test'];
            $output = new ConsoleOutput();
            $queryBuilder = new Builder();
            return new Installer($container->get('pdoTest'), $settings['install-file'], $output, $queryBuilder);
        };

        return $app;
    }
}
