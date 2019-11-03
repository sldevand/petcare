<?php

namespace Tests\E2E\Pet;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class PetEndpointTest
 * @package Tests\E2E\Pet
 */
class PetEndpointTest extends TestCase
{
    protected $http;

    public function setUp()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $authClient = new Client([
            'base_uri' => 'http://petcare/auth/',
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
            'base_uri' => 'http://petcare/',
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

    public function tearDown()
    {
        $this->http = null;
    }
}
