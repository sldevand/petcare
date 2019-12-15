<?php

namespace Framework\Db\Pdo\Query;

use Exception;
use Framework\Api\Query\BuilderInterface;
use Framework\Db\Pdo\Adapter\YamlToTableAdapter;

/**
 * Class Builder
 * @package Framework\Db\Pdo
 */
class Builder implements BuilderInterface
{
    /**
     * @param string $entityFile
     * @return string
     * @throws Exception
     */
    public function createTable(string $entityFile): string
    {
        $adapter = new YamlToTableAdapter();
        $tableData = $adapter->adapt($entityFile);
        $table = new Table($tableData);

        return $table->toSql();
    }
}
