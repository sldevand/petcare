<?php

namespace Framework\Db\Pdo\Query\Constraint\ForeignKey;

use Framework\Db\Pdo\Query\Constraint\Constraint;

/**
 * Class ForeignKey
 * @package Framework\Db\Pdo\Query\Constraint\ForeignKey
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

    /**
     * @param ReferenceOption $referenceOption
     * @return ForeignKey
     */
    public function addReferenceOption(ReferenceOption $referenceOption): ForeignKey
    {
        $this->referenceOptions[] = $referenceOption;

        return $this;
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        $referenceOptionsPart = '';

        $referenceOptionsArr = [];
        if (!empty($this->referenceOptions)) {
            foreach ($this->referenceOptions as $referenceOption) {
                $referenceOptionsArr[] = $referenceOption->toSql();
            }

            $referenceOptionsPart = implode(' ', $referenceOptionsArr);
        }

        return <<<SQL
FOREIGN KEY($this->column) REFERENCES $this->parentTable($this->parentColumnName) $referenceOptionsPart
SQL;
    }
}
