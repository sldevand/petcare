<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Entity\PetCareEntity;
use App\Modules\Pet\Model\Repository\PetCareRepository;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;

/**
 * Class PetCareRepositoryTest
 * @package Tests\Integration\Pet\Repository
 */
class PetCareRepositoryTest extends TestCase
{
    /** @var PetCareRepository */
    protected static $petCareRepository;

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
        self::$petCareRepository = $container->get('petCareRepository');
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $beforePetCare = new PetCareEntity([
            'petId' => 1,
            'careId' => 2
        ]);

        $newPetCare = self::$petCareRepository->save($beforePetCare);
        $beforePetCare->setId($newPetCare->getId());

        $this->assertEquals($beforePetCare, $newPetCare, 'Can\'t save PetCare');
    }

    protected function tearDown()
    {
        self::$db->exec("PRAGMA foreign_keys=ON");
        self::$db->exec("DELETE FROM petCare;");
    }
}
