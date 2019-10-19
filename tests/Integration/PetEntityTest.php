<?php

namespace Tests\Integration;

use App\Modules\Pet\Model\Entity\PetEntity;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class PetEntityTest
 * @package Tests\Integration
 */
class PetEntityTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetFields()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', 'imageId' => 1, "createdAt" => $now
        ];
        $entity = new PetEntity($attributes);
        $fields = $entity->getFields();
        $this->assertNotEmpty($fields);
    }
}
