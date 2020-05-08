<?php

namespace Framework\Db\Pdo\Sqlite\Query;

use Framework\Db\Pdo\Query\AbstractTable;

/**
 * Class CreateTable
 * @package Framework\Db\Pdo\Sqlite\Query
 */
class CreateTable extends AbstractTable
{
    /**
     * @return string
     */
    public function toSql(): string
    {
        $fieldsSql = $this->getFieldsSql();
        $constraintsPart = $this->getConstraintsSql();
        $endPart = $fieldsSql;
        if (!empty($constraintsPart)) {
            $endPart .= <<<SQL
,
    $constraintsPart
SQL;
        }

        return <<<SQL
CREATE TABLE IF NOT EXISTS $this->name
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    $endPart
);
SQL;
    }
}
