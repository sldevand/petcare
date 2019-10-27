<?php

namespace Tests\Integration\Db\Pdo\Query;

use Exception;
use Framework\Db\Pdo\Query\Field;

/**
 * Class FieldTest
 * @package Tests\Integration\Db\Pdo\Query
 */
class FieldTest extends BaseQueryTest
{
    /**
     * @throws Exception
     */
    public function testFieldHydrate()
    {
        $fieldData = self::$mocks->getFieldData();

        $expectedField = new Field();
        $expectedField
            ->setName($fieldData['name'])
            ->setType($fieldData['type'])
            ->setSize($fieldData['size']);

        $field = new Field($fieldData);

        $this->assertEquals($field, $expectedField);
    }
}
