<?php

namespace Framework\Api\Query;

/**
 * Interface BuilderInterface
 * @package Framework\Api\Query
 */
interface BuilderInterface
{
    /**
     * @param array $entityConfig
     * @return string
     */
    public function createTable(array $entityConfig): string;
}
