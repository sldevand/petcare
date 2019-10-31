<?php

namespace Tests\Integration\User\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Entity\UserEntity;
use App\Modules\User\Model\Repository\UserRepository;
use DateTime;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;
use Tests\Integration\Pet\Provider\PetImageEntityProvider;

/**
 * Class UserRepositoryTest
 * @package Tests\Integration\User\Repository
 */
class UserRepositoryTest extends TestCase
{
    /** @var UserRepository */
    protected static $userRepository;

    /** @var PetRepository */
    protected static $petRepository;

    /** @var PDO $db */
    protected static $db;

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass()
    {
        $app = BaseTestFramework::generateApp();
        $container = $app->getContainer();
        self::$db = $container->get('pdoTest');
        $container->get('installerTest')->execute();
        self::$userRepository = $container->get('userRepository');
        self::$petRepository = $container->get('petRepository');
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $pets = [];
        foreach ($this->getPets() as $pet) {
            $pets[] = self::$petRepository->save($pet);
        }

        $now = (new DateTime())->format('Y-m-d H:i:s');

        $attributes = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'foo@bar.com',
            'password' => 'p@SSw0rd',
            'createdAt' => $now,
            'pets' => $pets
        ];

        $user = new UserEntity($attributes);
        $savedUser = self::$userRepository->save($user);
        $user->setId($savedUser->getId());

        $this->assertEquals($user, $savedUser);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPets()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $dogImageEntity = PetImageEntityProvider::getPetImages()['dog'];
        $dogAttributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog', 'createdAt' => $now, 'image' => $dogImageEntity
        ];
        $dogEntity = new PetEntity($dogAttributes);

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $catImageEntity = PetImageEntityProvider::getPetImages()['cat'];
        $attributesToUpdate = [
            'name' => 'elie', 'dob' => '15/10/2014', 'specy' => 'cat', 'createdAt' => $now, 'image' => $catImageEntity
        ];
        $catEntity = new PetEntity($attributesToUpdate);

        return [
            $dogEntity,
            $catEntity
        ];
    }
}
