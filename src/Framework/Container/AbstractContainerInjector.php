<?php

namespace Framework\Container;

use Psr\Container\ContainerInterface;

/**
 * Class AbstractContainerInjector
 * @package Framework\Container
 */
abstract class AbstractContainerInjector
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * AbstractContainerInjector constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
