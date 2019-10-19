<?php

namespace Tests\Integration;

use App\Setup\InstallDatabase;
use Lib\Resource\PDOFactory;

/**
 * Class BaseTestFramework
 * @package Tests\Integration
 */
class BaseTestFramework
{
    /**
     * @return \Slim\App
     */
    public static function generateApp()
    {
        $settings = require __DIR__ . '/../../src/settings.php';
        $app = new \Slim\App($settings);
        $container = $app->getContainer();

        $container['pdo'] = function ($c) {
            $settings = $c->get('settings')['pdo']['test'];
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };

        $container['installDatabase'] = function ($c) {
            $settings = $c->get('settings')['pdo']['test'];
            return new InstallDatabase($c->get('pdo'), $settings['install-file']);
        };

        return $app;
    }
}
