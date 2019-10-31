<?php

namespace Tests\Integration\Framework\Db\Pdo\Query;

use Exception;
use Framework\Db\Pdo\Query\Builder;

/**
 * Class BuilderTest
 * @package Tests\Integration\Db\Pdo\Query
 */
class BuilderTest extends BaseQueryTest
{
    /**
     * @throws Exception
     */
    public function testCreateTable()
    {
        $file = __DIR__ . '/../../../data/testValid.yaml';

        $builder = new Builder();
        $sql = $builder->createTable($file);
        $expectedSql = <<<SQL
CREATE TABLE IF NOT EXISTS test
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    testProperty TEXT NOT NULL,
    FOREIGN KEY(id) REFERENCES test1(testId) ON DELETE CASCADE
);
SQL;
        $this->assertEquals($expectedSql, $sql);
    }
}
