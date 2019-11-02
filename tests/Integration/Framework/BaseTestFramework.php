<?php

namespace Tests\Integration\Framework;

use App\Common\Setup\Installer;
use App\Modules\Care\Model\Repository\CareRepository;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Repository\UserPetRepository;
use App\Modules\User\Model\Repository\UserRepository;
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
            if (file_exists($settings['db-file'])) {
                unlink($settings['db-file']);
            }
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
            return new PetImageRepository($container->get('pdoTest'), $container->get('defaultValidator'));
        };

        $container['petRepository'] = function (ContainerInterface $container) {
            return new PetRepository($container->get('pdoTest'), $container->get('defaultValidator'), $container->get('petImageRepository'));
        };

        $container['userPetRepository'] = function (ContainerInterface $container) {
            return new UserPetRepository($container->get('pdoTest'), $container->get('defaultValidator'));
        };

        $container['userRepository'] = function (ContainerInterface $container) {
            return new UserRepository($container->get('pdoTest'), $container->get('defaultValidator'), $container->get('userPetRepository'));
        };

        $container['careRepository'] = function (ContainerInterface $container) {
            return new CareRepository($container->get('pdoTest'), $container->get('defaultValidator'));
        };

        return $app;
    }
}
