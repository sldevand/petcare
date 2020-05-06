<?php

namespace Framework\Db\Pdo\Query;

use Exception;
use Framework\Api\Query\BuilderInterface;
use Framework\Db\Pdo\Adapter\SqlSchemaToTableAdapter;
use Framework\Db\Pdo\Adapter\YamlToTableAdapter;
use PDO;

/**
 * Class Builder
 * @package Framework\Db\Pdo
 */
class Builder implements BuilderInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Builder constructor.
     * @param PDO $pdo
     */
    public function __construct(
        PDO $pdo
    )
    {
        $this->pdo = $pdo;
    }

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

    /**
     * @param string $tableName
     * @param string $entityFile
     * @return string
     * @throws Exception
     */
    public function alterTable(string $tableName, string $entityFile): string
    {
        $sqlSchemaAdapter = new SqlSchemaToTableAdapter($this->pdo);
        $oldTableData = $sqlSchemaAdapter->adapt($tableName);
        $oldTable = new Table($oldTableData);

        $yamlAdapter = new YamlToTableAdapter();
        $newTableData = $yamlAdapter->adapt($entityFile);

        $table = new AlterTable($oldTable, $newTableData);

        if ($table->hasChanges()) {
            return $table->toSql();
        }

        return '';
    }
}
