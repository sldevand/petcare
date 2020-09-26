<?php

namespace Framework\Db\Pdo\Adapter;

use Exception;
use Framework\Db\Pdo\Query\Constraint\Constraint;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ForeignKey;
use Framework\Db\Pdo\Query\Constraint\ForeignKey\ReferenceOption;
use Framework\Db\Pdo\Query\Constraint\Unique;
use Framework\Db\Pdo\Query\Field;
use Framework\Model\Validator\YamlEntityValidator;

/**
 * Class YamlToTableAdapter
 * @package Framework\Db\Pdo\Adapter
 */
class YamlToTableAdapter
{
    /** @var array */
    protected $tableData;

    /** @var array */
    protected $uniqueColumns = [];

    /**
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public function adapt(string $file): array
    {
        $validator = new YamlEntityValidator($file);
        $yamlConfig = $validator->getYaml();

        $this->tableData = [
            'name' => $yamlConfig['table']
        ];

        $fields = [];
        foreach ($yamlConfig['fields'] as $propertyName => $fieldConfig) {
            $fieldConfig['name'] = $propertyName;
            $field = $this->createField($fieldConfig);

            if (!empty($fieldConfig['size'])) {
                $field->setSize($fieldConfig['size']);
            }

            if (!empty($fieldConfig['constraints'])) {
                $field = $this->addConstraints($fieldConfig['constraints'], $field);
            }

            if ($field->getName() === 'id') {
                continue;
            }
            $fields[$field->getName()] = $field;
        }

        if (!empty($this->uniqueColumns)) {
            $this->tableData['constraints'][] = new Unique(['columns' => $this->uniqueColumns]);
        }

        $this->tableData['fields'] = $fields;

        return $this->tableData;
    }

    /**
     * @param array $constraint
     * @param \Framework\Db\Pdo\Query\Field $field
     * @return \Framework\Db\Pdo\Query\Constraint\ForeignKey\ForeignKey
     * @throws \Exception
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
            $foreignKey->addReferenceOption(
                new ReferenceOption(
                    [
                        'on' => $cascade,
                        'action' => 'cascade'
                    ]
                )
            );
        }

        return $foreignKey;
    }

    /**
     * @param array $constraints
     * @param \Framework\Db\Pdo\Query\Field $field
     * @return \Framework\Db\Pdo\Query\Field
     * @throws \Exception
     */
    protected function addConstraints(array $constraints, Field $field)
    {
        foreach ($constraints as $key => $constraint) {
            if ($key === 'nullable' && $constraint === false) {
                $field->addConstraint(new Constraint(['name' => 'NOT NULL']));
            }

            if ($key === 'unique' && $constraint === true) {
                $this->uniqueColumns[] = $field->getColumn();
            }

            if ($key !== 'fk') {
                continue;
            }

            $this->tableData['constraints'][] = $this->getForeignKey($constraint, $field);
        }

        return $field;
    }

    /**
     * @param array $fieldConfig
     * @return \Framework\Db\Pdo\Query\Field
     * @throws \Exception
     */
    protected function createField(array $fieldConfig)
    {
        $field = new Field();
        return $field
            ->setName($fieldConfig['name'])
            ->setColumn($fieldConfig['column'])
            ->setType($fieldConfig['type']);
    }
}
