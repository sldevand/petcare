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

    /** @var \App\Modules\Activation\Model\Repository\PasswordResetRepository */
    public static $activationRepository;

    /** @var array */
    public static $user = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'password'
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

        $container = $app->getContainer();

        self::$userRepository = $container->get('userRepository');
        self::$activationRepository = $container->get('activationRepository');

        $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
        $dotEnv->load(__DIR__ . '/../.env');
        self::$websiteUrl = $_ENV['WEBSITE_URL'];
    }

    public function testSubscribe()
    {
        $url = self::$websiteUrl . '/user/subscribe';
        $res = $this->postWithBody($url, self::$user);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals($jsonResponse['status'], "1");

        self::$subscribedUser = $jsonResponse['data'];
        self::assertEquals(self::$user['email'], self::$subscribedUser['email']);
    }

    public function testLoginBeforeActivation()
    {
        self::expectException(\GuzzleHttp\Exception\ClientException::class);
        self::expectExceptionMessage(
            '{"status":0,"errors":"User is not activated, please click the link in your email to activate the account!"}'
        );

        $url = self::$websiteUrl . '/user/login';
        $user = self::$user;
        unset($user['firstName']);
        unset($user['lastName']);

        $this->postWithBody($url, $user);
    }


    public function testActivation()
    {
        $user = self::$userRepository->fetchOneBy("email", self::$user['email']);
        $id = $user->getId();

        $activation = self::$activationRepository->fetchOneBy('userId', $id);

        $activationCode = $activation->getActivationCode();

        $url = self::$websiteUrl . "/user/activate/$id/$activationCode";

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $res = $client->get($url);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);

        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);
        self::assertEquals(self::$user['email'], $jsonContents['data']['email']);
        self::assertEquals(1, $jsonContents['data']['activated']);
    }

    public function testLoginAfterActivation()
    {
        $url = self::$websiteUrl . '/user/login';
        $user = self::$user;

        $res = $this->postWithBody($url, $user);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);

        self::assertEquals(self::$user['email'], $jsonContents['data']['email']);
        self::assertNotEmpty($jsonContents['data']['apiKey']);
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
