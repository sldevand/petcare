<?php

namespace Framework\Db\Pdo\Query;

use Framework\Api\Db\Pdo\Query\TableInterface;
use Framework\Db\Pdo\Query\Constraint\Constraint;

/**
 * Class AbstractTable
 * @package Framework\Db\Pdo\Query
 */
abstract class AbstractTable extends Hydratable implements TableInterface
{
    /** @var string */
    protected $name = '';

    /** @var Field[] */
    protected $fields = [];

    /** @var Constraint[] */
    protected $constraints = [];

    /**
     * @param Field $field
     * @return AbstractTable
     */
    public function addField(Field $field): AbstractTable
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @param Constraint $constraint
     * @return AbstractTable
     */
    public function addConstraint(Constraint $constraint): AbstractTable
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
     * @return AbstractTable
     */
    public function setName(string $name): AbstractTable
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
     * @return AbstractTable
     */
    public function setFields(array $fields): AbstractTable
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
     * @return AbstractTable
     */
    public function setConstraints(array $constraints): AbstractTable
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
}
