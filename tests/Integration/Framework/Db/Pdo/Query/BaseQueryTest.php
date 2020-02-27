<?php

namespace Tests\Integration\Framework\Db\Pdo\Query;

use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\Db\Pdo\Query\Mock\TableMocks;

abstract class BaseQueryTest extends TestCase
{
    /** @var TableMocks */
    public static $mocks;


    public static function setUpBeforeClass(): void
    {
        self::$mocks = new TableMocks();
    }
}
