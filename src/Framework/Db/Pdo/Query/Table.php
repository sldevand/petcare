<?php

namespace Framework\Db\Pdo\Query;

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
    public function toSql(): string
    {
        $fields = [];
        foreach ($this->getFields() as $field) {
            $fields[] = $field->toSql();
        }

        $fieldsPart = implode(',', $fields);

        $pk = $this->name . '_PK';

        $constraintsPart = '';
        $constraintPartArr = [];
        if (!empty($this->getConstraints())) {
            foreach ($this->getConstraints() as $constraint) {
                $constraintPartArr[] = $constraint->toSql();
            }
            $constraintsPart = implode(',' . PHP_EOL . '    ', $constraintPartArr);
        }

        return <<<SQL
CREATE TABLE IF NOT EXISTS $this->name
(
    id INTEGER NOT NULL,
    $fieldsPart,
    CONSTRAINT $pk PRIMARY KEY (id),
    $constraintsPart
);
SQL;
    }
}
