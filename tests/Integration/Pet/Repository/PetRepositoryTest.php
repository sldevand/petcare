<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Image\Service\ImageManager;
use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Entity\PetImageEntity;
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

    /** @var ImageManager */
    protected static $imageManager;

    /** @var PDO $db */
    protected static $db;


    public static function setUpBeforeClass(): void
    {
        $app = BaseTestFramework::generateApp();
        $container = $app->getContainer();
        self::$db = $container->get('pdoTest');
        $container->get('installerTest')->execute();
        self::$petRepository = $container->get('petRepository');
        self::$imageManager = $container->get('imageManager');
    }

    public function setUp(): void
    {
        self::$db->exec("PRAGMA foreign_keys=ON");
        self::$db->exec('DELETE FROM pet;');
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
        $newEntity->setCreatedAt($updatedEntity->getCreatedAt());

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
            'id' => $afterEntity->getId(),
            'name' => 'rox',
            'dob' => '22/11/2010',
            'specy' => 'dog',
            'createdAt' => $afterEntity->getCreatedAt()
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
            'id' => $afterEntity->getId(),
            'name' => 'elie',
            'dob' => '15/10/2014',
            'specy' => 'cat',
            'image' => $catImageEntity,
            'createdAt' => $afterEntity->getCreatedAt()
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
            $petAfter = self::$petRepository->fetchOne($id);
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
        $dogImageEntity = clone PetImageEntityProvider::getPetImages()['dog'];
        $afterEntity->setImage($dogImageEntity);
        $entityWithImageSaved = self::$petRepository->save($afterEntity);
        $entityWithImageFetched = self::$petRepository->fetchImage($afterEntity);

        $encodedImage = self::$imageManager->getImageFromPath($dogImageEntity->getImage());
        $dogImageEntity->setImage($encodedImage);
        $dogImageEntity->setThumbnail($encodedImage);
        $dogImageEntity->setId($entityWithImageSaved->getImage()->getId());
        $entityWithImageSaved->setImage($dogImageEntity);

        $this->assertEquals($entityWithImageSaved, $entityWithImageFetched, 'Can\'t save PetEntity');
    }

    /**
     * @throws Exception
     */
    public function testFetchAll()
    {
        $images = PetImageEntityProvider::getPetImages();

        $dogAttributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog'
        ];
        $dogEntity = new PetEntity($dogAttributes);

        $catAttributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', 'image' => $images['cat']
        ];
        $catEntity = new PetEntity($catAttributes);

        $savedDogEntity = self::$petRepository->save($dogEntity);
        $savedCatEntity = self::$petRepository->save($catEntity);

        $catImageEntity = $savedCatEntity->getImage();
        $encodedImage = self::$imageManager->getImageFromPath($catImageEntity->getImage());
        $catImageEntity->setImage($encodedImage);
        $catImageEntity->setThumbnail($encodedImage);
        $savedCatEntity->setImage($catImageEntity);

        $expected = [
            $savedDogEntity,
            $savedCatEntity
        ];

        $actual = self::$petRepository->fetchAll();

        $this->assertEquals($expected, $actual);
    }


    public function testFetchAllByField()
    {
        $dogEntity = new PetEntity(
            [
                'name' => 'waf', 'dob' => '2014-05-14T14:58:00.000Z', 'specy' => 'dog'
            ]
        );

        $elie = new PetEntity(
            [
                'name' => 'elie', 'dob' => '2013-10-15T14:58:00.000Z', 'specy' => 'cat'
            ]
        );

        $oliver = new PetEntity(
            [
                'name' => 'oliver', 'dob' => '2016-10-15T14:58:00.000Z', 'specy' => 'cat'
            ]
        );

        $milo = new PetEntity(
            [
                'name' => 'milo', 'dob' => '2015-10-15T14:58:00.000Z', 'specy' => 'cat'
            ]
        );


        self::$petRepository->save($dogEntity);
        $savedElie   = self::$petRepository->save($elie);
        $savedOliver = self::$petRepository->save($oliver);
        $savedMilo   = self::$petRepository->save($milo);

        $expected = [
            $savedOliver,
            $savedMilo,
            $savedElie
        ];

        $options = ['orderBy' => 'dob', 'direction' => 'desc'];

        $actual = self::$petRepository->fetchAllByField('specy', 'cat', $options);
        $this->assertEquals($expected, $actual);

        $expected = [
            $savedElie,
            $savedMilo,
            $savedOliver
        ];

        $options = ['orderBy' => 'dob', 'direction' => 'asc'];

        $actual = self::$petRepository->fetchAllByField('specy', 'cat', $options);
        $this->assertEquals($expected, $actual);

        $expected = [
            $savedMilo,
            $savedOliver
        ];

        $options = ['orderBy' => 'dob', 'direction' => 'asc', 'offset' => 1, 'limit' => 2];
        $actual = self::$petRepository->fetchAllByField('specy', 'cat', $options);
        $this->assertEquals($expected, $actual);

        $options = ['orderBy' => 'dob', 'direction' => 'asc', 'offset' => 1];
        $this->expectExceptionMessage('You must specify a limit when offset is set');
        self::$petRepository->fetchAllByField('specy', 'cat', $options);
    }

    protected function tearDown(): void
    {
        self::$db->exec("PRAGMA foreign_keys=ON");
        self::$db->exec('DELETE FROM pet;');
    }
}
