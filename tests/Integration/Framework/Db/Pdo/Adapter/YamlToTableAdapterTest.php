<?php

namespace Tests\Integration\Framework\Db\Pdo\Adapter;

use Exception;
use Framework\Db\Pdo\Adapter\YamlToTableAdapter;
use Framework\Db\Pdo\Query\Constraint\Constraint;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ForeignKey;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ReferenceOption;
use Framework\Db\Pdo\Query\Field;
use PHPUnit\Framework\TestCase;

/**
 * Class YamlToTableAdapterTest
 * @package Tests\Integration\Db\Pdo\Adapter
 */
class YamlToTableAdapterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAdapt()
    {
        $fields = [
            'testProperty' =>
                new Field(
                    [
                        'name' => 'testProperty',
                        'column' => 'testProperty',
                        'type' => 'TEXT',
                        'constraints' => [
                            new Constraint(
                                [
                                    'name' => 'NOT NULL'
                                ]
                            )
                        ]
                    ]
                )
        ];

        $constraints = [
            new ForeignKey(
                [
                    'column' => 'id',
                    'parentTable' => 'test1',
                    'parentColumnName' => 'testId',
                    'referenceOptions' => [
                        new ReferenceOption(
                            [
                                'on' => 'delete',
                                'action' => 'cascade'
                            ]
                        )
                    ]
                ]
            )
        ];

        $expectedTableData = [
            'name' => 'test',
            'constraints' => $constraints,
            'fields' => $fields
        ];

        $file = __DIR__ . '/../../../data/testValid.yaml';
        $adpter = new YamlToTableAdapter();
        $tableData = $adpter->adapt($file);

        $this->assertEquals($expectedTableData, $tableData);
    }
}
