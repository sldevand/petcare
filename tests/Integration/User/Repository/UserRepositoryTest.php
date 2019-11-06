<?php

namespace Tests\Integration\User\Repository;

use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Entity\UserEntity;
use App\Modules\User\Model\Repository\UserRepository;
use DateTime;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;
use Tests\Integration\Pet\Provider\PetEntityProvider;
use Tests\Integration\User\Provider\UserEntityProvider;

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
        foreach (PetEntityProvider::getPets() as $pet) {
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
     * @throws Exception
     */
    public function testFetchAll()
    {
        $this->tearDown();
        $users = UserEntityProvider::getUsers();

        $expected = [];
        foreach ($users as $user) {
            $expected[] = self::$userRepository->save($user);
        }

        $actual = self::$userRepository->fetchAll();

        $this->assertEquals($expected, $actual);
    }

    protected function tearDown()
    {
        self::$db->exec("PRAGMA foreign_keys=ON");
        self::$db->exec('DELETE FROM user;');
        self::$db->exec('DELETE FROM pet;');
    }
}
