<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use Exception;
use Framework\Exception\RepositoryException;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;
use Tests\Integration\Pet\Provider\PetImageEntityProvider;

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
    public function testCreate()
    {
        $attributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];

        $beforeEntity = new PetEntity($attributes);
        $newEntity = self::$petRepository->create($beforeEntity);
        $beforeEntity->setId($newEntity->getId());

        $this->assertEquals($beforeEntity, $newEntity, 'Can\'t create PetEntity');
    }


    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat'
        ];

        $beforeEntity = new PetEntity($attributes);
        $afterEntity = self::$petRepository->create($beforeEntity);
        $beforeEntity->setId($afterEntity->getId());
        $this->assertEquals($beforeEntity, $afterEntity, 'Can\'t create PetEntity');

        $attributesToUpdate = [
            'id' => $afterEntity->getId(), 'name' => 'rox', 'dob' => '22/11/2010', 'specy' => 'dog'
        ];

        $newEntity = new PetEntity($attributesToUpdate);
        $updatedEntity = self::$petRepository->update($newEntity);

        $this->assertEquals($newEntity, $updatedEntity, 'The two Pet entities are not equal');
        $this->assertNotEquals($afterEntity, $updatedEntity, 'The two Pet entities are equal');
    }


    /**
     * @throws Exception
     */
    public function testSaveWithoutPetImageEntity()
    {
        $attributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];

        $beforeEntity = new PetEntity($attributes);
        $afterEntity = self::$petRepository->save($beforeEntity);
        $beforeEntity->setId($afterEntity->getId());

        $this->assertEquals($beforeEntity, $afterEntity, 'Can\'t save PetEntity');

        $attributesToUpdate = [
            'id' => $afterEntity->getId(), 'name' => 'rox', 'dob' => '22/11/2010', 'specy' => 'dog'
        ];

        $newEntity = new PetEntity($attributesToUpdate);
        $updatedEntity = self::$petRepository->save($newEntity);

        $this->assertEquals($newEntity, $updatedEntity, 'The two Pet entities are not equal');
        $this->assertNotEquals($afterEntity, $updatedEntity, 'The two Pet entities are equal');
    }

    /**
     * @throws Exception
     */
    public function testSaveWithPetImageEntity()
    {
        $dogImageEntity = PetImageEntityProvider::getPetImages()['dog'];

        $dogAttributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog', 'image' => $dogImageEntity
        ];

        $beforeEntity = new PetEntity($dogAttributes);
        $afterEntity = self::$petRepository->save($beforeEntity);
        $beforeEntity->setId($afterEntity->getId());
        $beforeEntity->setImage($afterEntity->getImage());

        $this->assertEquals($beforeEntity, $afterEntity, 'Can\'t save PetEntity');

        $catImageEntity = PetImageEntityProvider::getPetImages()['cat'];

        $attributesToUpdate = [
            'id' => $afterEntity->getId(), 'name' => 'elie', 'dob' => '15/10/2014', 'specy' => 'cat', 'image' => $catImageEntity
        ];

        $newEntity = new PetEntity($attributesToUpdate);
        $updatedEntity = self::$petRepository->save($newEntity);
        $updatedEntity->setImage($newEntity->getImage());

        $this->assertEquals($newEntity, $updatedEntity, 'The two Pet entities are not equal');
        $this->assertNotEquals($afterEntity, $updatedEntity, 'The two Pet entities are equal');
    }

    /**
     * @throws Exception
     */
    public function testDeleteOne()
    {
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat'
        ];

        $beforeEntity = new PetEntity($attributes);
        $afterEntity = self::$petRepository->create($beforeEntity);
        $id = $afterEntity->getId();

        $result = self::$petRepository->deleteOne($id);
        $this->assertTrue($result === true, 'could not delete this entity');

        try {
            $petAfter = self::$petRepository->findOne($id);
        } catch (RepositoryException $e) {
            $petAfter = false;
        }
        $this->assertTrue($petAfter === false);
    }

    /**
     * @throws Exception
     */
    public function testFetchImage()
    {
        //Save PetEntity without image
        $dogAttributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];

        $beforeEntity = new PetEntity($dogAttributes);
        $afterEntity = self::$petRepository->save($beforeEntity);
        $beforeEntity->setId($afterEntity->getId());

        //Save new PetImage and attach it to PetEntity
        $dogImageEntity = PetImageEntityProvider::getPetImages()['dog'];
        $afterEntity->setImage($dogImageEntity);
        $entityWithImageSaved = self::$petRepository->save($afterEntity);
        $entityWithImageFetched = self::$petRepository->fetchImage($afterEntity);

        $this->assertEquals($entityWithImageSaved, $entityWithImageFetched, 'Can\'t save PetEntity');
    }

    protected function tearDown()
    {
        self::$db->exec("PRAGMA foreign_keys=ON");
        self::$db->exec('DELETE FROM pet;');
    }
}
