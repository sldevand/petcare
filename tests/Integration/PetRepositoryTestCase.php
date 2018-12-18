<?php

namespace Tests\Integration;

use App\Model\Entity\PetEntity;
use App\Model\Repository\PetRepository;
use App\Model\Resource\PDOFactory;

/**
 * Class PetRepositoryTestCase
 * @package Tests\Integration
 */
class PetRepositoryTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PetRepository
     */
    protected static $petRepository;


    /**
     * @var \PDO $db
     */
    protected static $db;

    public static function setUpBeforeClass()
    {
        require_once 'Setup/setup-1.0.0.php';
        self::$db = PDOFactory::getSqliteConnexion(__DIR__ . '/../../database/petcare-test.db');
        self::$petRepository = new PetRepository(self::$db);
    }

    public function setUp()
    {
        $attributes = [
            "name" => "elie", "age" => '5', "specy" => 'cat'
        ];
        $entity = new PetEntity($attributes);
        self::$petRepository->create($entity);
    }


    public function testCreate()
    {
        $attributes = [
            "name" => "waf", "age" => '5', "specy" => 'dog'
        ];
        $entity = new PetEntity($attributes);
        $result = self::$petRepository->create($entity);
        $this->assertTrue($result === true, "Can't create entity");
        $pet = self::$petRepository->findOne(2);
        $entity->setId(2);

        $this->assertEquals($entity, $pet, 'THe two entities are not equal');
    }

    public function testUpdate()
    {
        $petBefore = self::$petRepository->findOne(1);

        $attributes = [
            "id" => 1, "name" => "wouf", "age" => 4, "specy" => 'dog'
        ];
        $entity = new PetEntity($attributes);
        self::$petRepository->update($entity);

        $pet = self::$petRepository->findOne(1);

        $this->assertNotEquals($petBefore, $pet, 'THe two entities are equal');
    }

    public function testDeleteOne()
    {
        $result = self::$petRepository->deleteOne(1);
        $this->assertTrue($result === true, "couldn't delete this entity");
        $petAfter = self::$petRepository->findOne(1);
        $this->assertTrue($petAfter === false);
    }

    protected function tearDown()
    {
        self::$db->exec("delete from pet_entity;");
    }

}