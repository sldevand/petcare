<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use Exception;
use Framework\Exception\RepositoryException;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;

/**
 * Class PetRepositoryTest
 * @package Tests\Integration\Pet
 */
class PetRepositoryTest extends TestCase
{
    /** @var PetRepository */
    protected static $petRepository;

    /** @var PDO $db */
    protected static $db;

    public static function setUpBeforeClass()
    {
        $app = BaseTestFramework::generateApp();
        $container = $app->getContainer();
        self::$db = $container->get('pdoTest');
        $container->get('installerTest')->execute();
        self::$petRepository = $container->get('petRepository');
    }

    /**
     * @throws Exception
     */
    public function setUp()
    {

        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat'
        ];
        $entity = new PetEntity($attributes);

        self::$petRepository->create($entity);
    }

    /**
     * @throws Exception
     */
    public function testCreate()
    {
        $attributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];

        $entity = new PetEntity($attributes);
        $result = self::$petRepository->create($entity);
        $this->assertTrue($result === true, 'Can\'t create entity');
        $pet = self::$petRepository->findOne(2);
        $entity->setId(2);

        $this->assertEquals($entity, $pet, 'THe two entities are not equal');
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $petBefore = self::$petRepository->findOne(1);
        $attributes = [
            'id' => 1, 'name' => 'wouf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];
        $entity = new PetEntity($attributes);
        self::$petRepository->update($entity);
        $pet = self::$petRepository->findOne(1);

        $this->assertNotEquals($petBefore, $pet, 'The two entities are equal');
    }

    public function testDeleteOne()
    {
        $result = self::$petRepository->deleteOne(1);
        $this->assertTrue($result === true, 'could not delete this entity');

        try {
            $petAfter = self::$petRepository->findOne(1);
        } catch (RepositoryException $e) {
            $petAfter = false;
        }
        $this->assertTrue($petAfter === false);
    }

    protected function tearDown()
    {
        self::$db->exec('DELETE FROM pet;');
    }
}
