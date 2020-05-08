<?php

namespace Tests\Functional\Care;

use App\Modules\Token\Helper\Token;
use App\Modules\User\Model\Entity\UserEntity;
use Tests\Functional\DefaultLifeCycleTest;

/**
 * Class CareLifeCycleTest
 * @package Tests\Functional\Care
 */
class CareLifeCycleTest extends DefaultLifeCycleTest
{
    /** @var string */
    public static $websiteUrl;

    /** @var \App\Modules\User\Model\Repository\UserRepository */
    public static $userRepository;

    /** @var \App\Modules\Pet\Model\Repository\PetRepository */
    public static $petRepository;

    /** @var \App\Modules\Care\Model\Repository\CareRepository */
    public static $careRepository;

    /** @var array */
    private static $petResponse;

    /** @var int */
    private static $careId;

    /** @var array */
    public static $pet = [
        'name' => 'TESTName',
        'specy' => 'cat',
        'dob' => '2015-10-25'
    ];

    /** @var array */
    public static $care = [
        'title' => 'Title here',
        'content' => 'Content here',
        'appointmentDate' => '2020-05-11 10:30:00'
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
//        $this->get('/{petName}/{careId}', 'careController:get');
//        $this->get('/{petName}', 'careController:get');
//        $this->put('/{petName}/{careId}', 'petController:put');
//        $this->delete('/{petName}/{careId}', 'careController:delete');

        $url = self::$websiteUrl . '/api/pets';
        $res = $this->postWithBody($url, self::$pet, true);
        $contents = $res->getBody()->getContents();
        self::$petResponse = \json_decode($contents, true);
        $petId = self::$petResponse['data']['id'];

        $url = self::$websiteUrl . '/api/cares/' . self::$pet['name'];

        $res = $this->postWithBody($url, self::$care, true);

        self::assertEquals("201", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals($jsonResponse['status'], "1");
        self::assertEquals($jsonResponse['message'], "Care has been saved!");

        $expected = [
            'petId' => $petId,
            'title' => 'Title here',
            'content' => 'Content here',
            'appointmentDate' => '2020-05-11 10:30:00'
        ];

        self::$careId = $jsonResponse['data']['id'];

        self::assertEquals($expected['petId'], $jsonResponse['data']['petId']);
        self::assertEquals($expected['title'], $jsonResponse['data']['title']);
        self::assertEquals($expected['content'], $jsonResponse['data']['content']);
        self::assertEquals($expected['appointmentDate'], $jsonResponse['data']['appointmentDate']);
    }

    public function testGetList()
    {
        $url = self::$websiteUrl . '/api/cares/' . self::$petResponse['data']['name'];

        $mockCares = [
            [
                'title' => 'Title here 3',
                'content' => 'Content here 3',
                'appointmentDate' => '2020-05-11 10:30:00'
            ],
            [
                'title' => 'Title here 4',
                'content' => 'Content here 4',
                'appointmentDate' => '2020-05-11 10:30:00'
            ]
        ];

        foreach ($mockCares as $mockCare) {
            $this->postWithBody($url, $mockCare, true);
        }

        $res = $this->get($url, true);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);
        self::assertEquals("1", $jsonResponse['status']);
        self::assertEquals("List of Cares", $jsonResponse['message']);
        self::assertEquals(3, count($jsonResponse['data']));
    }

    /**
     * @throws \Framework\Exception\RepositoryException
     */
//    public function testGetOne()
//    {
//        $url = self::$websiteUrl . '/api/cares/' . self::$petResponse['data']['name'] . '/' . self::$careId;
//
//        $fetchedCare = self::$petRepository->fetchOne(self::$careId);
//
//        $res = $this->get($url, true);
//
//        self::assertEquals("200", $res->getStatusCode());
//        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
//        $contents = $res->getBody()->getContents();
//        $jsonResponse = \json_decode($contents, true);
//        self::assertEquals("1", $jsonResponse['status']);
//        self::assertEquals("Informations on self::$careId", $jsonResponse['message']);
//
//        $expectedData = [
//            'id' => $fetchedCare->getId(),
//            'name' => 'TESTName',
//            'dob' => '2015-10-25',
//            'specy' => 'cat',
//            'image' => null
//        ];
//
//        self::assertEquals($expectedData, $jsonResponse['data']);
//    }
//

    /**
     * @throws \Exception
     */
    public static function tearDownAfterClass()
    {
        self::$userRepository->deleteOneBy('email', self::$mockedUser['email']);
    }
}
