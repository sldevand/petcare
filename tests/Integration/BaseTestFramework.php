<?php

namespace Tests\Integration;

use App\Common\Setup\InstallDatabase;
use Framework\Resource\PDOFactory;
use Psr\Container\ContainerInterface;
use Slim\App;

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
        $settings = require __DIR__ . '/../../src/settings.php';
        $app = new App($settings);
        $container = $app->getContainer();

        $container['pdo'] = function (ContainerInterface $c) {
            $settings = $c->get('settings')['pdo']['test'];
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };

        $container['installDatabase'] = function (ContainerInterface $c) {
            $settings = $c->get('settings')['pdo']['test'];
            return new InstallDatabase($c->get('pdo'), $settings['install-file']);
        };

        return $app;
    }
}
