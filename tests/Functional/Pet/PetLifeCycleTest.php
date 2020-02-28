<?php

namespace Tests\Functional\User;

use App\Modules\Token\Helper\Token;
use App\Modules\User\Model\Entity\UserEntity;
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
        'dob' => '2015-10-25'
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

        $user = new UserEntity(self::$mockedUser);
        $token = new Token();
        $apiKey = $token->generate($user, self::$settings['settings']['jwt']['secret']);
        $user->setApiKey($apiKey);

        self::$savedUser = self::$userRepository->save($user);
    }

    public function testPost()
    {
        $url = self::$websiteUrl . '/api/pets';
        $res = $this->postWithBody($url, self::$pet, true);

        self::assertEquals("201", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals($jsonResponse['status'], "1");
        self::assertEquals($jsonResponse['message'], "Pet has been saved!");

        $expected = [
            'name' => 'TESTName',
            'specy' => 'cat',
            'dob' => '2015-10-25'
        ];

        $expected['id'] = self::$savedUser->getId();

        self::assertEquals($expected['name'], $jsonResponse['data']['name']);
        self::assertEquals($expected['specy'], $jsonResponse['data']['specy']);
        self::assertEquals($expected['dob'], $jsonResponse['data']['dob']);
    }

    public function testGetList()
    {
        $url = self::$websiteUrl . '/api/pets';

        $mockPets = [
            [
                'name' => 'TESTName5',
                'specy' => 'dog',
                'dob' => '2012-11-11'
            ],
            [
                'name' => 'TESTName6',
                'specy' => 'dog',
                'dob' => '2010-10-11'
            ]
        ];

        foreach ($mockPets as $mockPet) {
            $this->postWithBody($url, $mockPet, true);
        }

        $res = $this->get($url, true);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals($jsonResponse['status'], "1");
        self::assertEquals($jsonResponse['message'], "List of pets");
        self::assertEquals(count($jsonResponse['data']), 3);
    }

    public function testGetOne()
    {
        $name = self::$pet['name'];
        $url = self::$websiteUrl . "/api/pets/$name";

        $fetchedPet = self::$petRepository->fetchOneBy('name', $name);

        $res = $this->get($url, true);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals("1", $jsonResponse['status']);
        self::assertEquals("Informations on $name", $jsonResponse['message']);

        $expectedData = [
            'id' => $fetchedPet->getId(),
            'name' => 'TESTName',
            'dob' => '2015-10-25',
            'specy' => 'cat'
        ];

        self::assertEquals($expectedData, $jsonResponse['data']);
    }

    public static function tearDownAfterClass()
    {
        self::$petRepository->deleteOneBy('name', self::$pet['name']);
        self::$userRepository->deleteOneBy('email', self::$mockedUser['email']);
    }
}
