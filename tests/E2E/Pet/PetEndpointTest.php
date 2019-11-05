<?php

namespace Tests\E2E\Pet;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class PetEndpointTest
 * @package Tests\E2E\Pet
 */
class PetEndpointTest extends TestCase
{
    protected $http;

    public function setUp()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');
        $host = getenv('TEST_HOST');

        $headers = [
            'Content-Type' => 'application/json'
        ];
        $authClient = new Client([
            'base_uri' => "http://$host/auth/",
            'headers' => $headers
        ]);
        $authorization = $authClient->get('generate');
        $bearer = json_decode($authorization->getBody()->getContents());

        $headers = [
            'Content-Type' => 'application/json',
            'AccessToken' => 'key',
            'Authorization' => "Bearer $bearer",
        ];
        $this->http = new Client([
            'base_uri' => "http://$host/",
            'headers' => $headers
        ]);
    }

    public function testGetAll()
    {
        $all = $this->http->get('api/pets');

        $code = $all->getStatusCode();
        $pets = json_decode($all->getBody()->getContents());

        self::assertEquals(200, $code);
        self::assertEquals([], $pets);
    }

    public function testCreate()
    {
        $body = json_encode([
            'name' => 'elie', 'dob' => '15/10/2014', 'specy' => 'cat'
        ]);

        $options = [
            'body' => $body
        ];

        $postResponse = $this->http->post('api/pets/new', $options);

        $code = $postResponse->getStatusCode();
        $pet = json_decode($postResponse->getBody()->getContents());

        var_dump($code);
        var_dump($pet);
        self::assertEquals(201, $code);
        self::assertEquals($body, $pet);
    }

    public function tearDown()
    {
        $this->http = null;
    }
}
