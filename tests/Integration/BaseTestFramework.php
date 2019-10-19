<?php

namespace Tests\Integration;

use App\Common\Setup\Installer;
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
    public static function generateApp()
    {
        require __DIR__ . '/../../src/bootstrap.php';
        $settings = require __DIR__ . '/../../src/settings.php';
        $app = new App($settings);
        $container = $app->getContainer();

        $container['pdoTest'] = function (ContainerInterface $c) {
            $settings = $c->get('settings')['pdo']['test'];
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };

        $container['installerTest'] = function (ContainerInterface $c) {
            $settings = $c->get('settings')['pdo']['test'];
            $output = new ConsoleOutput();
            return new Installer($c->get('pdoTest'), $settings['install-file'], $output);
        };

        return $app;
    }
}
