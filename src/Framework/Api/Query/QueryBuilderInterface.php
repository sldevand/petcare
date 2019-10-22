<?php

namespace Framework\Api\Query;

/**
 * Interface QueryBuilderInterface
 * @package Framework\Api\Query
 */
interface QueryBuilderInterface
{
    public function createTable($name);

    public function addNullableConstraint($name);

    public function addForeignKey($name);

    public function addCascadePart();
}
