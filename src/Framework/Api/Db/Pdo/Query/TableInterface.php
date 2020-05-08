<?php

namespace Framework\Api\Db\Pdo\Query;

/**
 * Interface TableInterface
 * @package Framework\Api\Db\Pdo\Query
 */
interface TableInterface
{
    /**
     * @return string
     */
    public function toSql();
}
