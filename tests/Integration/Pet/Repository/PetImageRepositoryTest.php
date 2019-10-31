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
     * @throws Exception
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
    public function testCreate()
    {
        $expected = clone self::$petImages['cat'];
        $result = self::$petImageRepository->create($expected);
        $expected->setId(1);

        $this->assertEquals($expected, $result, 'Can\'t create entity');
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $beforeEntity = clone self::$petImages['cat'];
        $beforePetImage = self::$petImageRepository->create($beforeEntity);

        $newEntity = clone self::$petImages['dog'];
        $newEntity->setId($beforePetImage->getId());
        $newPetImage = self::$petImageRepository->update($newEntity);

        $this->assertNotEquals($beforePetImage, $newPetImage, 'The two entities are equal');
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $beforePetImage = clone self::$petImages['cat'];
        $afterPetImage = self::$petImageRepository->save($beforePetImage);
        $beforePetImage->setId(3);

        $this->assertEquals($beforePetImage, $afterPetImage, 'Can\'t save PetImage');

        $newEntity = clone self::$petImages['dog'];
        $newEntity->setId($beforePetImage->getId());
        $updatedPetImage = self::$petImageRepository->save($newEntity);

        $this->assertNotEquals($beforePetImage, $updatedPetImage, 'The two PetImage entities are equal');
    }

    /**
     * @throws Exception
     */
    public function testDeleteOne()
    {
        $beforeEntity = clone self::$petImages['cat'];
        $beforePetImage = self::$petImageRepository->create($beforeEntity);
        $id = $beforePetImage->getId();

        $result = self::$petImageRepository->deleteOne($id);
        $this->assertTrue($result === true, 'could not delete this entity');

        try {
            $petAfter = self::$petImageRepository->findOne($id);
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
