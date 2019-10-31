<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Repository\PetImageRepository;
use Exception;
use Framework\Exception\RepositoryException;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;
use Tests\Integration\Pet\Provider\PetImageEntityProvider;

/**
 * Class PetImageRepositoryTest
 * @package Tests\Integration\Pet\Repository
 */
class PetImageRepositoryTest extends TestCase
{
    /** @var PetImageRepository */
    protected static $petImageRepository;

    /** @var array */
    protected static $petImages;

    /** @var PDO $db */
    protected static $db;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        $app = BaseTestFramework::generateApp();
        $container = $app->getContainer();
        self::$db = $container->get('pdoTest');
        $container->get('installerTest')->execute();
        self::$petImageRepository = $container->get('petImageRepository');
        self::$petImages = PetImageEntityProvider::getPetImages();
    }

    /**
     * @throws Exception
     */
    public function setUp()
    {
        $entity = self::$petImages['cat'];

        self::$petImageRepository->create($entity);
    }

    /**
     * @throws Exception
     */
    public function testCreate()
    {
        $entity = self::$petImages['cat'];
        $result = self::$petImageRepository->create($entity);

        $this->assertTrue($result === true, 'Can\'t create entity');

        $pet = self::$petImageRepository->findOne(2);
        $entity->setId(2);

        $this->assertEquals($entity, $pet, 'THe two entities are not equal');
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $petBefore = self::$petImageRepository->findOne(1);

        $newEntity = self::$petImages['dog'];
        $newEntity->setId(1);
        self::$petImageRepository->update($newEntity);

        $pet = self::$petImageRepository->findOne(1);

        $this->assertNotEquals($petBefore, $pet, 'The two entities are equal');
    }

    public function testDeleteOne()
    {
        $result = self::$petImageRepository->deleteOne(1);
        $this->assertTrue($result === true, 'could not delete this entity');

        try {
            $petAfter = self::$petImageRepository->findOne(1);
        } catch (RepositoryException $e) {
            $petAfter = false;
        }
        $this->assertTrue($petAfter === false);
    }

    protected function tearDown()
    {
        self::$db->exec('DELETE FROM petImage;');
    }
}
