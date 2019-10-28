<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use DateTime;
use Exception;
use Framework\Exception\RepositoryException;
use Framework\Model\Validator\DefaultValidator;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;

/**
 * Class PetImageRepositoryTest
 * @package Tests\Integration\Pet\Repository
 */
class PetImageRepositoryTest extends TestCase
{
    /** @var PetImageRepository */
    protected static $petImageRepository;

    /** @var array */
    protected static $encodedImages;

    /** @var PDO $db */
    protected static $db;

    public static function setUpBeforeClass()
    {
        $app = BaseTestFramework::generateApp();
        $container = $app->getContainer();
        self::$db = $container->get('pdoTest');
        $container->get('installerTest')->execute();
        self::$petImageRepository = new PetImageRepository(self::$db, new DefaultValidator());

        $catFile = __DIR__ . '/../data/cat.jpeg';
        $dogFile = __DIR__ . '/../data/dog.jpeg';

        self::addEncodedImage('cat', $catFile);
        self::addEncodedImage('dog', $dogFile);
    }

    /**
     * @param string $key
     * @param string $file
     */
    protected static function addEncodedImage(string $key, string $file)
    {
        $contents = file_get_contents($file);
        self::$encodedImages[$key] = base64_encode($contents);
    }

    /**
     * @throws Exception
     */
    public function setUp()
    {
        $attributes = ['image' => self::$encodedImages['cat']];

        $entity = new PetImageEntity($attributes);

        self::$petImageRepository->create($entity);
    }

    /**
     * @throws Exception
     */
    public function testCreate()
    {
        $attributes = ['image' => self::$encodedImages['cat']];

        $entity = new PetImageEntity($attributes);
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

        $attributes = ['id' => 1, 'image' => self::$encodedImages['dog']];
        $entity = new PetImageEntity($attributes);
        self::$petImageRepository->update($entity);
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
