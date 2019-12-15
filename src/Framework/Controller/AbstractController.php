<?php

namespace Framework\Controller;

use Framework\Api\Repository\RepositoryInterface;
use Framework\Observer\Subject;

/**
 * Class AbstractController
 * @package Framework\Controller
 */
abstract class AbstractController extends Subject
{
    /** @var RepositoryInterface */
    protected $repository;

    public function __construct(
        RepositoryInterface $repository
    ) {
        parent::__construct();
        $this->repository = $repository;
    }
}
