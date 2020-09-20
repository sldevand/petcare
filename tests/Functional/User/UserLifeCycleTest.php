<?php

namespace Tests\Functional\User;

use GuzzleHttp\Client;
use Tests\Functional\DefaultLifeCycleTest;

/**
 * Class UserLifeCycleTest
 * @package Tests\Functional\Usery
 */
class UserLifeCycleTest extends DefaultLifeCycleTest
{
    /** @var string */
    public static $websiteUrl;

    /** @var \App\Modules\User\Model\Repository\UserRepository */
    public static $userRepository;

    /** @var \App\Modules\Activation\Model\Repository\NotificationRepository */
    public static $activationRepository;

    /** @var \App\Modules\PasswordReset\Model\Repository\PasswordResetRepository */
    public static $passwordResetRepository;

    /** @var string */
    public static $newPassword = 'J0HnD032';

    /** @var array */
    public static $user = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'password'
    ];

    /** @var array */
    public static $subscribedUser = [];

    public static function setUpBeforeClass(): void
    {
        $container = self::getContainer();

        self::$userRepository = $container->get('userRepository');
        self::$activationRepository = $container->get('activationRepository');
        self::$passwordResetRepository = $container->get('passwordResetRepository');

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

    public function testPasswordReset()
    {
        $url = self::$websiteUrl . '/user/passwordReset';
        $user = [
            'email' => self::$user['email']
        ];

        $res = $this->postWithBody($url, $user);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);

        self::assertEquals(1, $jsonContents['status']);
        self::assertEquals(
            "We sent you an email, please click the link in it to reset your password",
            $jsonContents['message']
        );
        self::assertEmpty($jsonContents['data']);
    }

    public function testPasswordChange()
    {
        $dbUser = self::$userRepository->fetchOneBy("email", self::$user['email']);
        $id = $dbUser->getId();
        $passwordReset = self::$passwordResetRepository->fetchOneBy('userId', $id);
        $resetCode = $passwordReset->getResetCode();

        $url = self::$websiteUrl . "/user/passwordChange/$id/$resetCode";

        $userWithNewPassword = [
            'email' => $dbUser->getEmail(),
            'newPassword' => self::$newPassword
        ];

        $res = $this->postWithBody($url, $userWithNewPassword);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);

        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);

        self::assertEquals(1, $jsonContents['status']);
        self::assertEquals(
            "You successfully changed your password.",
            $jsonContents['message']
        );
        self::assertEmpty($jsonContents['data']);
    }

    public function testLoginFailAfterPasswordChanged()
    {
        self::expectException(\GuzzleHttp\Exception\ClientException::class);
        self::expectExceptionMessage(
            '{"status":0,"errors":"Wrong Password!"}'
        );

        $url = self::$websiteUrl . '/user/login';
        $user = [
            'email' => self::$user['email'],
            "password" => self::$user['password']
        ];

        $this->postWithBody($url, $user);
    }

    public function testLoginAfterPasswordChanged()
    {
        $url = self::$websiteUrl . '/user/login';
        $user = [
            'email' => self::$user['email'],
            "password" => self::$newPassword
        ];

        $res = $this->postWithBody($url, $user);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonContents = \json_decode($contents, true);

        self::assertEquals(self::$user['email'], $jsonContents['data']['email']);
        self::assertNotEmpty($jsonContents['data']['apiKey']);
    }

    public static function tearDownAfterClass(): void
    {
        self::$userRepository->deleteOneBy('email', self::$user['email']);
    }
}
