<?php

namespace Framework\Db\Pdo\Query;

/**
 * Class Field
 * @package Framework\Db\Pdo\Query
 */
class Field extends Hydratable
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var string */
    protected $size;

    /** @var Constraint[] */
    protected $constraints;

    /**
     * @param Constraint $constraint
     * @return Field
     */
    public function addConstraint(Constraint $constraint): Field
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
     * @return Field
     */
    public function setName(string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type): Field
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return Field
     */
    public function setSize(string $size): Field
    {
        $this->size = $size;
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
     * @return Field
     */
    public function setConstraints(array $constraints): Field
    {
        $this->constraints = $constraints;
        return $this;
    }
}
