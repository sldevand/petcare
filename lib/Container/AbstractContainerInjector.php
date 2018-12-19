<?php

namespace Lib\Container;

use Psr\Container\ContainerInterface;

/**
 * Class AbstractContainerInjector
 */
abstract class AbstractContainerInjector
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * PetController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
