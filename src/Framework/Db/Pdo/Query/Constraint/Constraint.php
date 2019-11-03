<?php

namespace Framework\Db\Pdo\Query\Constraint;

use Framework\Db\Pdo\Query\Hydratable;

/**
 * Class Constraint
 * @package Framework\Db\Pdo\Query
 */
class Constraint extends Hydratable
{
    /** @var string */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Constraint
     */
    public function setName(string $name): Constraint
    {
        $this->name = strtoupper($name);

        return $this;
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return $this->name;
    }
}
