<?php

namespace Tests\Integration\Framework\Db\Pdo\Query;

use Exception;
use Framework\Db\Pdo\Query\Constraint\Constraint;
use Framework\Db\Pdo\Query\Field;
use Framework\Db\Pdo\Sqlite\Query\CreateTable;

/**
 * Class TableTest
 * @package Tests\Integration\Db\Pdo\Query
 */
class TableTest extends BaseQueryTest
{
    /**
     * @throws Exception
     */
    public function testTableHydrate()
    {
        $name = 'test';
        $fieldsData = self::$mocks->getFieldsData();
        $constraintsData = [
            self::$mocks->getConstraintData()
        ];

        $constraints = [];
        foreach ($constraintsData as $data) {
            $constraints[] = new Constraint($data);
        }

        $fields = [];
        foreach ($fieldsData as $data) {
            $fields[] = new Field($data);
        }

        $tableData = [
            'name' => $name,
            'fields' => $fields,
            'constraints' => $constraints
        ];

        $expectedTable = new CreateTable();
        $expectedTable
            ->setName($name)
            ->addConstraint($constraints[0])
            ->setFields($fields);

        $table = new CreateTable($tableData);

        $this->assertEquals($expectedTable, $table);
    }
}
