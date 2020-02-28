<?php

namespace Tests\Functional\User;

use App\Modules\User\Model\Entity\UserEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Tests\Functional\DefaultLifeCycleTest;

/**
 * Class PetLifeCycleTest
 * @package Tests\Functional\User
 */
class PetLifeCycleTest extends DefaultLifeCycleTest
{
    /** @var string */
    public static $websiteUrl;

    /** @var \App\Modules\User\Model\Repository\UserRepository */
    public static $userRepository;

    /** @var \App\Modules\Pet\Model\Repository\PetRepository */
    public static $petRepository;

    /** @var array */
    public static $pet = [
        'name' => 'TESTName',
        'specy' => 'cat',
        'dob' => '2015-10-25',
        'image' => ''
    ];

    /** @var array */
    public static $user = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'foo@bar.com',
        'password' => 'p@SSw0rd',
        'apiKey' => 'gsdgsdgsF.resfsfd.essdffsd'
    ];

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        $container = self::getContainer();

        self::$userRepository = $container->get('userRepository');
        self::$petRepository = $container->get('petRepository');

        $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
        $dotEnv->load(__DIR__ . '/../.env');
        self::$websiteUrl = $_ENV['WEBSITE_URL'];

        $user = new UserEntity(self::$user);
        self::$userRepository->save($user);
    }

    public function testPost()
    {
        $url = self::$websiteUrl . '/api/pets';
        $res = $this->postWithBody($url, self::$pet);

        self::assertEquals("201", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals($jsonResponse['status'], "1");

        self::assertEquals(self::$pet, $jsonResponse['data']);
    }

    /**
     * @param string $url
     * @param array $body
     * @return mixed
     */
    public function postWithBody(string $url, array $body)
    {
        $options =
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . self::$user['apiKey']
                ]
            ];

        $client = new Client($options);

        return $client->post(
            $url,
            ['body' => json_encode($body)]
        );
    }

    public static function tearDownAfterClass()
    {
        self::$petRepository->deleteOneBy('name', self::$pet['name']);
        self::$userRepository->deleteOneBy('email', self::$user['email']);
    }
}
