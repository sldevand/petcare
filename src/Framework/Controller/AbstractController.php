<?php

namespace Framework\Controller;

use Framework\Api\Repository\RepositoryInterface;

/**
 * Class AbstractController
 * @package Framework\Controller
 */
class AbstractController
{
    /** @var RepositoryInterface */
    protected $repository;

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
}
