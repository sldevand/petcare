<?php

namespace Framework\Db\Pdo\Query;

use Framework\Db\Pdo\Query\Constraint\Constraint;

/**
 * Class Table
 * @package Framework\Db\Pdo\Query
 */
class Table extends Hydratable
{
    /** @var string */
    protected $name = '';

    /** @var Field[] */
    protected $fields = [];

    /** @var Constraint[] */
    protected $constraints = [];

    /**
     * @param Field $field
     * @return Table
     */
    public function addField(Field $field): Table
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @param Constraint $constraint
     * @return Table
     */
    public function addConstraint(Constraint $constraint): Table
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Table
     */
    public function setName(string $name): Table
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     * @return Table
     */
    public function setFields(array $fields): Table
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param Constraint[] $constraints
     * @return Table
     */
    public function setConstraints(array $constraints): Table
    {
        $this->constraints = $constraints;

        return $this;
    }


    /**
     * @return string
     */
    protected function getFieldsSql(): string
    {
        $fields = [];
        foreach ($this->getFields() as $field) {
            $fields[] = $field->toSql();
        }
        return implode(',', $fields);
    }

    /**
     * @return string
     */
    protected function getConstraintsSql(): string
    {
        $constraintPartArr = [];
        if (!empty($this->getConstraints())) {
            foreach ($this->getConstraints() as $constraint) {
                $constraintPartArr[] = $constraint->toSql();
            }
        }
        return implode(',' . PHP_EOL . '    ', $constraintPartArr);
    }

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
