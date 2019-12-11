<?php

namespace Tests\Functional\User;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Slim\App;

/**
 * Class UserLifeCycleTest
 * @package Tests\Functional\Usery
 */
class UserLifeCycleTest extends TestCase
{
    /** @var string */
    public static $websiteUrl;

    /** @var \App\Modules\User\Model\Repository\UserRepository */
    public static $userRepository;

    /** @var array */
    public static $user = [
        'firstName' => 'John',
        'lastName'  => 'Doe',
        'email'     => 'john@doe.com',
        'password'  => 'password'
    ];

    /** @var array */
    public static $subscribedUser = [];

    public static function setUpBeforeClass()
    {
        require __DIR__ . '/../../../src/bootstrap.php';
        require VENDOR_DIR . '/autoload.php';
        $settings = require SRC_DIR . '/settings.php';
        $app = new App($settings);
        require SRC_DIR . '/dependencies.php';

        self::$userRepository = $container->get('userRepository');

        $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
        $dotEnv->load(__DIR__ . '/../.env');
        self::$websiteUrl = $_ENV['WEBSITE_URL'];
    }

    public function testSubscribe()
    {
        $url = self::$websiteUrl . '/user/subscribe';
        $res = $this->postWithBody($url, self::$user);

        self::assertEquals("201", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        self::$subscribedUser = \json_decode($contents, true);
        self::assertEquals(self::$user['email'], self::$subscribedUser['email']);
    }

    public function testLogin()
    {
        $url = self::$websiteUrl . '/user/login';
        $user = self::$user;
        unset($user['firstName']);
        unset($user['lastName']);

        $res = $this->postWithBody($url, $user);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);

        self::assertEquals(self::$subscribedUser['email'], $jsonContents['email']);
        self::assertEquals(self::$subscribedUser['apiKey'], $jsonContents['apiKey']);
    }

    /**
     * @param string $url
     * @param array $body
     * @return mixed
     */
    public function postWithBody(string $url, array $body)
    {
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return $client->post(
            $url,
            ['body' => json_encode($body)]
        );
    }

    public static function tearDownAfterClass()
    {
        self::$userRepository->deleteOneBy('email', self::$user['email']);
    }
}
