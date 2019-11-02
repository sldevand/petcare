<?php

namespace Tests\Integration\Pet\Entity;

use App\Modules\Pet\Model\Entity\PetEntity;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Pet\Provider\PetImageEntityProvider;

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
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', "createdAt" => $now
        ];
        $entity = new PetEntity($attributes);
        $fields = $entity->getFields();
        $this->assertNotEmpty($fields);
        $this->assertTrue(is_array($fields));
    }

    /**
     * @throws Exception
     */
    public function testJsonSerialize()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', 'createdAt' => $now
        ];
        $entity = new PetEntity($attributes);
        $encodedJson = json_encode($entity);
        $jsonStr = <<<JSON
{"name":"elie","dob":"13\/10\/2014","specy":"cat","createdAt":"$now","updatedAt":null,"image":null,"cares":[],"id":null}
JSON;
        $this->assertNotEmpty($encodedJson, "Json entity is empty");
        $this->assertEquals($jsonStr, $encodedJson, "Json entity is not well encoded");
    }

    /**
     * @throws Exception
     */
    public function testHydrate()
    {
        $petImages = PetImageEntityProvider::getPetImages();

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'id' => 8,
            'name' => 'elie',
            'dob' => '13/10/2014',
            'specy' => 'cat',
            'image' => $petImages['cat'],
            'createdAt' => $now,
            'updatedAt' => $now
        ];
        $entity = new PetEntity($attributes);
        self::assertTrue($entity->getId() === 8);
        self::assertTrue($entity->getName() === 'elie');
        self::assertTrue($entity->getDob() === '13/10/2014');
        self::assertTrue($entity->getSpecy() === 'cat');
        self::assertEquals($entity->getImage(), $petImages['cat']);
        self::assertTrue($entity->getCreatedAt() === $now);
        self::assertTrue($entity->getUpdatedAt() === $now);
    }
}
