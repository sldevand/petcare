<?php

namespace Framework\Db\Pdo\Query\Constraint;

/**
 * Class Unique
 * @package Framework\Db\Pdo\Query\Constraint
 */
class Unique extends Constraint
{
    /** @var array */
    protected $columns;

    /**
     * @param string $column
     * @return Unique
     */
    public function addColumn(string $column): Unique
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     * @return Unique
     */
    public function setColumns(array $columns): Unique
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        $columnsSql = implode(',', $this->columns);

        return <<<SQL
UNIQUE($columnsSql)
SQL;
    }
}
