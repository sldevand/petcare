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
        // Post one pet
        $petsUrl = self::$websiteUrl . '/api/pets';
        $petResponse = $this->postWithBody($petsUrl, self::$pet, true);
        self::$petResponse = \json_decode($petResponse->getBody()->getContents(), true);
        $petId = self::$petResponse['data']['id'];

        //Post one care
        $oneCarePostUrl = self::$websiteUrl . '/api/cares/' . self::$pet['name'];
        $oneCarePostResponse = $this->postWithBody($oneCarePostUrl, self::$care, true);
        $oneCarePostDecodedResponse = \json_decode($oneCarePostResponse->getBody()->getContents(), true);

        self::assertEquals("201", $oneCarePostResponse->getStatusCode());
        self::assertEquals("application/json", $oneCarePostResponse->getHeader('content-type')[0]);

        $expected = [
            'petId' => $petId,
            'title' => 'Title here',
            'content' => 'Content here',
            'appointmentDate' => '2020-05-11 10:30:00'
        ];

        self::assertEquals("1", $oneCarePostDecodedResponse['status']);
        self::assertEquals("Care Title here has been saved!", $oneCarePostDecodedResponse['message']);

        foreach ($expected as $key => $value) {
            self::assertEquals($value, $oneCarePostDecodedResponse['data'][$key]);
        }
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
        $contents = $res->getBody()->getContents();
        $jsonResponse = \json_decode($contents, true);

        self::assertEquals("200", $res->getStatusCode());
        self::assertEquals("application/json", $res->getHeader('content-type')[0]);
        self::assertEquals("1", $jsonResponse['status']);
        self::assertEquals("List of Cares", $jsonResponse['message']);
        self::assertEquals(3, count($jsonResponse['data']));
    }

    /**
     * @throws \Framework\Exception\RepositoryException
     */
    public function testGetOne()
    {
        // Post one pet
        $petsUrl = self::$websiteUrl . '/api/pets';
        $petResponse = $this->postWithBody($petsUrl, self::$pet, true);
        $petDecodedResponse = \json_decode($petResponse->getBody()->getContents(), true);
        $petName = $petDecodedResponse['data']['name'];

        //Post one care
        $oneCarePostUrl = self::$websiteUrl . '/api/cares/' . self::$pet['name'];
        $oneCarePostResponse = $this->postWithBody($oneCarePostUrl, self::$care, true);
        $oneCarePostDecodedResponse = \json_decode($oneCarePostResponse->getBody()->getContents(), true);

        //Get one Care
        $oneCareGetUrl = self::$websiteUrl . '/api/cares/' . $petName . '/' . $oneCarePostDecodedResponse['data']['id'];
        $oneCareGetResponse = $this->get($oneCareGetUrl, true);
        $oneCareGetDecodedResponse = \json_decode($oneCareGetResponse->getBody()->getContents(), true);
        $title = $oneCareGetDecodedResponse['data']['title'];

        self::assertEquals("200", $oneCareGetResponse->getStatusCode());
        self::assertEquals("application/json", $oneCareGetResponse->getHeader('content-type')[0]);
        self::assertEquals("1", $oneCareGetDecodedResponse['status']);
        self::assertEquals($oneCarePostDecodedResponse['data'], $oneCareGetDecodedResponse['data']);
        self::assertEquals("Care $title for $petName", $oneCareGetDecodedResponse['message']);
    }

    /**
     * @throws \Framework\Exception\RepositoryException
     */
    public function testPut()
    {
        // Post one pet
        $petsUrl = self::$websiteUrl . '/api/pets';
        $petResponse = $this->postWithBody($petsUrl, self::$pet, true);
        $petDecodedResponse = \json_decode($petResponse->getBody()->getContents(), true);
        $petName = $petDecodedResponse['data']['name'];

        //Post one care
        $oneCarePostUrl = self::$websiteUrl . '/api/cares/' . self::$pet['name'];
        $oneCarePostResponse = $this->postWithBody($oneCarePostUrl, self::$care, true);
        $oneCarePostDecodedResponse = \json_decode($oneCarePostResponse->getBody()->getContents(), true);

        $putData = $oneCarePostDecodedResponse['data'];

        $putData['content'] = 'testPut';

        //Put one Care
        $oneCarePutUrl = self::$websiteUrl . '/api/cares/' . $petName . '/' . $oneCarePostDecodedResponse['data']['id'];
        $oneCarePutResponse = $this->putWithBody($oneCarePutUrl, $putData, true);
        $oneCarePutDecodedResponse = \json_decode($oneCarePutResponse->getBody()->getContents(), true);
        $title = $oneCarePutDecodedResponse['data']['title'];

        $putData['updatedAt'] = $oneCarePutDecodedResponse['data']['updatedAt'];

        self::assertEquals("200", $oneCarePutResponse->getStatusCode());
        self::assertEquals("application/json", $oneCarePutResponse->getHeader('content-type')[0]);
        self::assertEquals("1", $oneCarePutDecodedResponse['status']);
        self::assertEquals($putData, $oneCarePutDecodedResponse['data']);
        self::assertEquals("Care $title has been saved!", $oneCarePutDecodedResponse['message']);
    }

    /**
     * @throws \Framework\Exception\RepositoryException
     */
    public function testDelete()
    {
        // Post one pet
        $petsUrl = self::$websiteUrl . '/api/pets';
        $petResponse = $this->postWithBody($petsUrl, self::$pet, true);
        $petDecodedResponse = \json_decode($petResponse->getBody()->getContents(), true);
        $petName = $petDecodedResponse['data']['name'];

        //Post one care
        $oneCarePostUrl = self::$websiteUrl . '/api/cares/' . self::$pet['name'];
        $oneCarePostResponse = $this->postWithBody($oneCarePostUrl, self::$care, true);
        $oneCarePostDecodedResponse = \json_decode($oneCarePostResponse->getBody()->getContents(), true);

        //Delete one Care
        $oneCareDeleteUrl = self::$websiteUrl . '/api/cares/' . $petName . '/' . $oneCarePostDecodedResponse['data']['id'];
        $oneCareGetResponse = $this->delete($oneCareDeleteUrl, true);
        $oneCareGetDecodedResponse = \json_decode($oneCareGetResponse->getBody()->getContents(), true);

        self::assertEquals("200", $oneCareGetResponse->getStatusCode());
        self::assertEquals("application/json", $oneCareGetResponse->getHeader('content-type')[0]);
        self::assertEquals("1", $oneCareGetDecodedResponse['status']);
        self::assertEquals($oneCarePostDecodedResponse['data'], $oneCareGetDecodedResponse['data']);
        self::assertEquals("Entity successfully deleted", $oneCareGetDecodedResponse['message']);
    }

    /**
     * @throws \Exception
     */
    public static function tearDownAfterClass()
    {
        self::$userRepository->deleteOneBy('email', self::$mockedUser['email']);
    }
}
