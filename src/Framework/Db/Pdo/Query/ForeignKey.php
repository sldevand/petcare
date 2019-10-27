<?php

namespace Framework\Db\Pdo\Query;

/**
 * Class ForeignKey
 * @package Framework\Db\Pdo\Query
 */
class ForeignKey extends Constraint
{
    /** @var string */
    protected $column;

    /** @var string */
    protected $parentTable;

    /** @var string */
    protected $parentColumnName;

    /** @var ReferenceOption[] */
    protected $referenceOptions;

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param string $column
     * @return ForeignKey
     */
    public function setColumn(string $column): ForeignKey
    {
        $this->column = $column;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentTable(): string
    {
        return $this->parentTable;
    }

    /**
     * @param string $parentTable
     * @return ForeignKey
     */
    public function setParentTable(string $parentTable): ForeignKey
    {
        $this->parentTable = $parentTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentColumnName(): string
    {
        return $this->parentColumnName;
    }

    /**
     * @param string $parentColumnName
     * @return ForeignKey
     */
    public function setParentColumnName(string $parentColumnName): ForeignKey
    {
        $this->parentColumnName = $parentColumnName;
        return $this;
    }

    /**
     * @return ReferenceOption[]
     */
    public function getReferenceOptions(): array
    {
        return $this->referenceOptions;
    }

    /**
     * @param ReferenceOption[] $referenceOptions
     * @return ForeignKey
     */
    public function setReferenceOptions(array $referenceOptions): ForeignKey
    {
        $this->referenceOptions = $referenceOptions;
        return $this;
    }
}
