<?php

namespace Tests\Integration\User\Provider;

use App\Modules\User\Model\Entity\UserEntity;
use DateTime;
use Exception;
use Tests\Integration\Pet\Provider\PetEntityProvider;

/**
 * Class UserEntityProvider
 * @package Tests\Integration\Pet\Provider
 */
class UserEntityProvider
{
    /**
     * @throws Exception
     */
    public static function getUsers()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        return [
            new UserEntity([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@mail.com',
                'password' => 'p@SSw0rd',
                'apiKey' => 'json.web.token',
                'pets' => [PetEntityProvider::getPets()[0]]
            ]),
            new UserEntity([
                'firstName' => 'Foo',
                'lastName' => 'Bar',
                'email' => 'foo.bar@mail.com',
                'password' => 'S3crEtPASS',
                'apiKey' => 'json.web.token',
                'pets' => [PetEntityProvider::getPets()[1]]
            ])
        ];
    }
}
