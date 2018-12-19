<?php
/**
 * Created by PhpStorm.
 * User: sln
 * Date: 19/12/18
 * Time: 17:53
 */

namespace Tests\Integration;


use App\Setup\InstallDatabase;
use Lib\Resource\PDOFactory;

class BaseTestFramework
{
    public static function generateApp(){
        $settings = require __DIR__ . '/../../src/settings.php';
        $app = new \Slim\App($settings);
        $container = $app->getContainer();

        $container['pdo'] = function ($c) {
            $settings = $c->get('settings')['pdo']['test'];
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };

        $container['installDatabase'] = function ($c) {
            $settings = $c->get('settings')['pdo']['test'];
            return new InstallDatabase($c->get('pdo'),$settings['install-file']);
        };

        return $app;
    }
}
