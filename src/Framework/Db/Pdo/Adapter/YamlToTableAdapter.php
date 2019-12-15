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

    /**
     * @param string $file
     * @return array
     * @throws Exception
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
            $field = new Field();
            $field
                ->setName($propertyName)
                ->setColumn($fieldConfig['column'])
                ->setType($fieldConfig['type']);

            if (!empty($fieldConfig['size'])) {
                $field->setSize($fieldConfig['size']);
            }

            if (!empty($fieldConfig['constraints'])) {
                /** @var array | bool $constraint */
                foreach ($fieldConfig['constraints'] as $key => $constraint) {
                    if ($key === 'nullable' && $constraint === false) {
                        $field->addConstraint(new Constraint(['name' => 'NOT NULL']));
                    }

                    if ($key === 'unique' && $constraint === true) {
                        $uniqueColumns[] = $field->getColumn();
                    }


                    if ($key !== 'fk') {
                        continue;
                    }

                    $this->tableData['constraints'][] = $this->getForeignKey($constraint, $field);
                }
            }

            if ($field->getName() === 'id') {
                continue;
            }
            $fields[] = $field;
        }

        if (!empty($uniqueColumns)) {
            $this->tableData['constraints'][] = new Unique(['columns' => $uniqueColumns]);
        }

        $this->tableData['fields'] = $fields;

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
