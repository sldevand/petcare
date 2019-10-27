<?php

namespace Tests\Integration\Db\Pdo\Query\Mock;

/**
 * Class TableMocks
 * @package Tests\Integration\Db\Pdo\Query\Mock
 */
class TableMocks
{
    /**
     * @return array
     */
    public function getFieldData(): array
    {
        return [
            'name' => 'id',
            'type' => 'integer',
            'size' => '11'
        ];
    }

    /**
     * @return array
     */
    public function getFieldsData(): array
    {
        return [
            $this->getFieldData(),
            [
                'name' => 'name',
                'type' => 'varchar',
                'size' => '256'
            ],
            [
                'name' => 'specy',
                'type' => 'varchar',
                'size' => '256'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getConstraintData(): array
    {
        return [
            'name' => 'NOT NULL'
        ];
    }
}
