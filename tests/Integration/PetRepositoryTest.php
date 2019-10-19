<?php

namespace Tests\Integration;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use DateTime;
use Exception;
use Framework\Model\Validator\DefaultValidator;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Class PetRepositoryTestCase
 * @package Tests\Integration
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
        self::$db = $container->get('pdo');
        $container->get('installDatabase')->execute();
        self::$petRepository = new PetRepository(self::$db, new DefaultValidator());
    }

    /**
     * @throws Exception
     */
    public function setUp()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', 'imageId' => 1, "createdAt" => $now
        ];
        $entity = new PetEntity($attributes);

        self::$petRepository->create($entity);
    }

    /**
     * @throws Exception
     */
    public function testCreate()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog', 'imageId' => 3, "createdAt" => $now
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
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'id' => 1, 'name' => 'wouf', 'dob' => '13/10/2014', 'specy' => 'dog', "createdAt" => $now
        ];
        $entity = new PetEntity($attributes);
        self::$petRepository->update($entity);
        $pet = self::$petRepository->findOne(1);

        $this->assertNotEquals($petBefore, $pet, 'The two entities are equal');
    }

    public function testDeleteOne()
    {
        $result = self::$petRepository->deleteOne(1);
        $this->assertTrue($result === true, 'couldn\'t delete this entity');
        $petAfter = self::$petRepository->findOne(1);
        $this->assertTrue($petAfter === false);
    }

    protected function tearDown()
    {
        self::$db->exec('DELETE FROM pet;');
    }
}
