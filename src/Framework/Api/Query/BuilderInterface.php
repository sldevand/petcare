<?php

namespace Framework\Api\Query;

/**
 * Interface BuilderInterface
 * @package Framework\Api\Query
 */
interface BuilderInterface
{
    /**
     * @param string $entityFile
     * @return string
     */
    public function createTable(string $entityFile): string;
}
