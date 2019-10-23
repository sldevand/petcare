<?php

namespace Framework\Db\Pdo\Query;

use Exception;
use Framework\Api\Query\BuilderInterface;

/**
 * Class Builder
 * @package Framework\Db\Pdo
 */
class Builder implements BuilderInterface
{
    const SQLITE_VALID_CASCADE_KEYWORDS = ['delete', 'update'];
    const SQLITE_VALID_DATATYPES = ['numeric', 'text', 'real', 'text', 'blob'];

    /**
     * @param array $entityConfig
     * @return string
     * @throws Exception
     */
    public function createTable(array $entityConfig): string
    {
        $table = $entityConfig['table'];
        $fields = $entityConfig['fields'];

        $fieldsPart = '';
        $foreignKeysPart = '';
        foreach ($fields as $property => $field) {
            if (!empty($field['column']) && $field['column'] !== 'id' && $property !== 'id') {
                $fieldsPart .= $field['column'] . " " . $field['type'];
            }

            if (!empty($field['constraints'])) {
                foreach ($field['constraints'] as $constraintKey => $constraintValue) {
                    if ($field['column'] !== 'id' && $property !== 'id') {
                        $fieldsPart .= $this->addNullableConstraint($constraintKey, $constraintValue);
                    }
                    $foreignKeysPart .= $this->addForeignKey($field['column'], $constraintKey, $constraintValue);
                }
            }

            if ($field['column'] !== 'id' && $property !== 'id') {
                $fieldsPart .= ',' . PHP_EOL . '    ';
            }
        }
        $pk = $table . '_PK';

        return <<<SQL
CREATE TABLE IF NOT EXISTS $table
(
    id INTEGER NOT NULL,
    $fieldsPart
    CONSTRAINT $pk PRIMARY KEY (id)$foreignKeysPart
);
SQL;
    }

    /**
     * @param string $constraintKey
     * @param array | string $constraintValue
     * @return string
     */
    public function addNullableConstraint(string $constraintKey, $constraintValue): string
    {
        if ($constraintKey !== 'nullable' || $constraintValue === true) {
            return '';
        }

        return ' NOT NULL';
    }

    /**
     * @param string $column
     * @param string $constraintKey
     * @param array | string $constraintValue
     * @return string
     * @throws Exception
     */
    protected function addForeignKey(string $column, string $constraintKey, $constraintValue): string
    {
        if ($constraintKey !== 'fk' || !is_array($constraintValue)) {
            return '';
        }

        $fkReference = $constraintValue["reference"];
        $fkTable = $constraintValue["table"];
        $cascadePart = $this->addCascadePart($constraintValue);

        return <<<FK
,
    FOREIGN KEY($column) REFERENCES $fkTable($fkReference) $cascadePart
FK;
    }

    /**
     * @param array $constraintValue
     * @return string
     * @throws Exception
     */
    protected function addCascadePart(array $constraintValue): string
    {
        if (empty($constraintValue['cascade'])) {
            return '';
        }

        $this->checkCascadeIsArray($constraintValue['cascade']);

        $cascadePart = '';
        foreach ($constraintValue['cascade'] as $cascadeAction) {
            $this->checkCascadeAction($cascadeAction);
            $cascadePart .= "ON " . strtoupper($cascadeAction) . " CASCADE ";
        }

        return $cascadePart;
    }

    /**
     * @param string $cascadeAction
     * @throws Exception
     */
    protected function checkCascadeAction(string $cascadeAction)
    {
        if (!in_array(strtolower($cascadeAction), self::SQLITE_VALID_CASCADE_KEYWORDS)) {
            $class = get_class($this);
            throw new Exception(
                "$class::addCascadePart -->
                 $cascadeAction is not a valid cascade action word"
            );
        }
    }

    /**
     * @param array $constraintValue
     * @throws Exception
     */
    protected function checkCascadeIsArray(array $constraintValue)
    {
        if (!is_array($constraintValue)) {
            $class = get_class($this);
            throw new Exception(
                "$class::addCascadePart --> cascade is not a YAML array
                (maybe you forgot the '- ' in front of your array element"
            );
        }
    }
}
