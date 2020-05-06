<?php

namespace Framework\Db\Pdo\Query;

/**
 * Class Table
 * @package Framework\Db\Pdo\Query
 */
class AlterTable extends Table
{
    /**
     * @var Table
     */
    protected $oldTable;

    /**
     * @var array
     */
    protected $fieldsToAdd = [];

    /**
     * @var array
     */
    protected $fieldsToRemove = [];

    /**
     * AlterTable constructor.
     * @param Table $oldTable
     * @param array $properties
     * @throws \Exception
     */
    public function __construct(Table $oldTable, array $properties = [])
    {
        parent::__construct($properties);

        $this->oldTable = $oldTable;
        $this->fieldsDiff();
    }

    protected function fieldsDiff()
    {
        $oldFieldsNames = array_keys($this->oldTable->getFields());
        $newFieldsNames = array_keys($this->getFields());

        $fieldNamesToAdd = array_diff($newFieldsNames, $oldFieldsNames);
        foreach ($fieldNamesToAdd as $toAdd) {
            $this->fieldsToAdd[$toAdd] = $this->fields[$toAdd];
        }

        $fieldNamesToRemove = array_diff($oldFieldsNames, $newFieldsNames);
        foreach ($fieldNamesToRemove as $toRemove) {
            $this->fieldsToRemove[$toRemove] = $this->oldTable->getFields()[$toRemove];
        }
    }

    /**
     * @return bool
     */
    public function hasChanges()
    {
        return !empty($this->fieldsToAdd) || !empty($this->fieldsToRemove);
    }

    /**
     * @return string
     */
    protected function getFieldsToAddSql(): string
    {
        $fields = [];
        foreach ($this->fieldsToAdd as $field) {
            $fields[] = 'ADD COLUMN ' . $field->toSql();
        }
        return implode(',', $fields);
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        $toAddSql = $this->getFieldsToAddSql();

        return <<<SQL
ALTER TABLE $this->name 
  $toAddSql
;
SQL;
    }
}
