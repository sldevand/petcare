<?php

namespace Tests\Integration\Framework;

use App\Common\Setup\Installer;
use App\Modules\Care\Model\Repository\CareRepository;
use App\Modules\Image\Service\ImageManager;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Repository\UserRepository;
use Framework\Db\Pdo\Query\Builder;
use Framework\Model\Validator\DefaultValidator;
use Framework\Modules\Installed\Model\Repository\InstalledRepository;
use Framework\Resource\PDOFactory;
use Framework\Service\FileManager;
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
     * @param array $settings
     * @return App
     */
    public static function generateApp($settings = []): App
    {
        require_once __DIR__ . '/../../../src/bootstrap.php';

        if (empty($settings)) {
            $settings = require __DIR__ . '/../../../src/settings.php';
        }

        $app = new App($settings);
        $container = $app->getContainer();

        $container['pdoTest'] = function (ContainerInterface $container) {
            $settings = $container->get('settings')['pdo']['test'];
            if (file_exists($settings['db-file'])) {
                unlink($settings['db-file']);
            }
            return PDOFactory::getSqliteConnexion($settings['db-file']);
        };

        $container['defaultValidator'] = function (ContainerInterface $container) {
            return new DefaultValidator();
        };

        $container['installedRepository'] = function (ContainerInterface $c) {
            return new InstalledRepository(
                $c->get('pdoTest'),
                $c->get('defaultValidator')
            );
        };
        $container['installerTest'] = function (ContainerInterface $container) {
            $settings = $container->get('settings')['pdo']['test'];
            $output = new ConsoleOutput();
            $queryBuilder = new Builder($container->get('pdoTest'));
            return new Installer(
                $container->get('pdoTest'),
                $output,
                $queryBuilder,
                $container->get('installedRepository')
            );
        };

        $container['fileManager'] = function (ContainerInterface $c) use ($settings) {
            return new FileManager();
        };

        $container['imageManager'] = function (ContainerInterface $c) use ($settings) {
            return new ImageManager($c->get('fileManager'), $settings);
        };

        $container['petImageRepository'] = function (ContainerInterface $container) {
            return new PetImageRepository($container->get('pdoTest'), $container->get('defaultValidator'));
        };

        $container['petRepository'] = function (ContainerInterface $container) {
            return new PetRepository(
                $container->get('pdoTest'),
                $container->get('defaultValidator'),
                $container->get('petImageRepository'),
                $container->get('careRepository'),
                $container->get('imageManager')
            );
        };


        $container['userRepository'] = function (ContainerInterface $container) {
            return new UserRepository(
                $container->get('pdoTest'),
                $container->get('defaultValidator'),
                $container->get('petRepository')
            );
        };

        $container['careRepository'] = function (ContainerInterface $container) {
            return new CareRepository($container->get('pdoTest'), $container->get('defaultValidator'));
        };

        return $app;
    }
}
