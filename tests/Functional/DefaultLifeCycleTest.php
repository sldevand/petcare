<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;

/**
 * Class DefaultLifeCycleTest
 * @package Tests\Functional
 */
abstract class DefaultLifeCycleTest extends TestCase
{
    /** @var \Psr\Container\ContainerInterface */
    private static $container = null;

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public static function getContainer()
    {
        if (empty(self::$container)) {
            require __DIR__ . '/../../src/bootstrap.php';
            require VENDOR_DIR . '/autoload.php';
            $settings = require SRC_DIR . '/settings.php';
            $container = new \Slim\Container($settings);
            require SRC_DIR . '/dependencies.php';
            self::$container = $container;
        }

        return self::$container;
    }
}
