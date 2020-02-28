<?php

namespace Tests\Functional;

use Framework\Resource\PDOFactory;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Class DefaultLifeCycleTest
 * @package Tests\Functional
 */
abstract class DefaultLifeCycleTest extends TestCase
{
    /** @var \Psr\Container\ContainerInterface */
    private static $container = null;

    public static $settings = [];

    /** @var \App\Modules\User\Model\Entity\UserEntity */
    public static $savedUser;

    /** @var array */
    public static $mockedUser = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'foo@bar.com',
        'password' => 'p@SSw0rd',
        'apiKey' => 'gsdgsdgsF.resfsfd.essdffsd'
    ];

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public static function getContainer()
    {
        if (empty(self::$container)) {
            require __DIR__ . '/../../src/bootstrap.php';
            require VENDOR_DIR . '/autoload.php';
            $settings = require SRC_DIR . '/settings.php';

            $container = new \Slim\Container($settings);
            require SRC_DIR . '/dependencies.php';
            $container['pdo'] = function (ContainerInterface $c) {
                $settings = $c->get('settings')['pdo']['prod'];

                return PDOFactory::getSqliteConnexion($settings['db-file']);
            };

            self::$container = $container;
            self::$settings = $settings;
        }

        return self::$container;
    }

    /**
     * @param string $url
     * @param array $body
     * @param bool $auth
     * @return mixed
     */
    public function postWithBody(string $url, array $body, bool $auth = false)
    {
        $options =
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ];

        if ($auth) {
            $options['headers']['Authorization'] = 'Bearer ' . self::$savedUser->getApiKey();
        }

        $client = new Client($options);

        return $client->post(
            $url,
            ['body' => json_encode($body)]
        );
    }

    /**
     * @param string $url
     * @param bool $auth
     * @return mixed
     */
    public function get(string $url, bool $auth = false)
    {
        $options =
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ];

        if ($auth) {
            $options['headers']['Authorization'] = 'Bearer ' . self::$savedUser->getApiKey();
        }

        $client = new Client($options);

        return $client->get(
            $url
        );
    }
}
