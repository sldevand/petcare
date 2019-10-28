<?php

namespace Framework\Db\Pdo\Query;

/**
 * Class ReferenceOption
 * @package Framework\Db\Pdo\Query
 */
class ReferenceOption extends Hydratable
{
    /** @var string */
    protected $on;

    /** @var string */
    protected $action;

    /**
     * @return string
     */
    public function getOn(): string
    {
        return $this->on;
    }

    /**
     * @param string $on
     * @return ReferenceOption
     */
    public function setOn(string $on): ReferenceOption
    {
        $this->on = strtoupper($on);

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return ReferenceOption
     */
    public function setAction(string $action): ReferenceOption
    {
        $this->action = strtoupper($action);

        return $this;
    }

    public function toSql()
    {
        return <<<SQL
ON $this->on $this->action
SQL;
    }
}
