<?php

namespace Tests\Integration\Framework\Db\Pdo\Query;

use Exception;
use Framework\Db\Pdo\Query\Constraint;
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
            ->setColumn($fieldData['column'])
            ->setType($fieldData['type'])
            ->setSize($fieldData['size']);

        $field = new Field($fieldData);

        $this->assertEquals($field, $expectedField);
    }

    /**
     * @throws Exception
     */
    public function testToString()
    {
        $fieldData = self::$mocks->getFieldData();
        $field = new Field($fieldData);

        foreach (self::$mocks->getConstraintsDatas() as $constraintsData) {
            $field->addConstraint(new Constraint($constraintsData));
        }

        $sql = $field->toSql();
        $expectedSql = <<<SQL
id INTEGER(11) NOT NULL UNIQUE
SQL;

        $this->assertEquals(strtolower($sql), strtolower($expectedSql));
    }
}
