<?php

namespace Tests\Integration\Care\Repository;

use App\Modules\Care\Model\Entity\CareEntity;
use App\Modules\Care\Model\Repository\CareRepository;
use DateTime;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;

/**
 * Class CareRepositoryTest
 * @package Tests\Integration\Care\Repository
 */
class CareRepositoryTest extends TestCase
{
    /** @var CareRepository */
    protected static $careRepository;

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
        self::$careRepository = $container->get('careRepository');
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $attributes = [
            'title' => 'Care 1',
            'content' => 'Test 1 description',
            'createdAt' => $now
        ];

        $care = new CareEntity($attributes);
        $savedCare = self::$careRepository->save($care);
        $care->setId($savedCare->getId());

        $this->assertEquals($care, $savedCare);
    }
}
