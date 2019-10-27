<?php

namespace Framework\Db\Pdo\Query;

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
        $this->name = $name;
        return $this;
    }
}
