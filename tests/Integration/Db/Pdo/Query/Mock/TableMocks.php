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
            'column' => 'id',
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
                'column' => 'name',
                'type' => 'varchar',
                'size' => '256'
            ],
            [
                'name' => 'specy',
                'column' => 'specy',
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

    /**
     * @return array
     */
    public function getConstraintsDatas(): array
    {
        return [
            [
                'name' => 'NOT NULL'
            ],
            [
                'name' => 'UNIQUE'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getEntityConfig(): array
    {
        return [
            'name' => 'NOT NULL'
        ];
    }
}
