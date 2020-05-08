<?php

namespace Framework\Db\Pdo\Sqlite\Adapter;

use Exception;
use Framework\Db\Pdo\Query\Constraint\Constraint;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ForeignKey;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ReferenceOption;
use Framework\Db\Pdo\Query\Field;
use PDO;

/**
 * Class SqliteSchemaToTableAdapter
 * @package Framework\Db\Pdo\Sqlite\Adapter
 */
class SqliteSchemaToTableAdapter
{
    /** @var array */
    protected $tableData;

    /** @var PDO */
    protected $pdo;

    /**
     * SqliteSchemaToTableAdapter constructor.
     * @param PDO $pdo
     */
    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }

    /**
     * @param string $tableName
     * @return array
     * @throws Exception
     */
    public function adapt(string $tableName): array
    {
        $sql = "pragma table_info($tableName);";
        $st = $this->pdo->query($sql);
        $tableSchema = $st->fetchAll(PDO::FETCH_ASSOC);

        $this->tableData['name'] = $tableName;

        foreach ($tableSchema as $key => $column) {
            if ($column['name'] === 'id') {
                continue;
            }

            $field = new Field();
            $field
                ->setName($column['name'])
                ->setColumn($column['name'])
                ->setType($column['type']);

            if ($column['notnull'] === '1') {
                $field->addConstraint(new Constraint(['name' => 'NOT NULL']));
            }

            $this->tableData['fields'][$field->getName()] = $field;
        }

        return $this->tableData;
    }

    /**
     * @param array $constraint
     * @param Field $field
     * @return ForeignKey
     * @throws Exception
     */
    protected function getForeignKey(array $constraint, Field $field): ForeignKey
    {
        $foreignKey = new ForeignKey();
        $foreignKey
            ->setParentTable($constraint['table'])
            ->setParentColumnName($constraint['reference'])
            ->setColumn($field->getColumn());

        if (empty($constraint['cascade'])) {
            return $foreignKey;
        }

        foreach ($constraint['cascade'] as $cascade) {
            $foreignKey->addReferenceOption(new ReferenceOption([
                'on' => $cascade,
                'action' => 'cascade'
            ]));
        }

        return $foreignKey;
    }
}
