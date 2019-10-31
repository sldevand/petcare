<?php

namespace Framework\Api\Repository;

use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use PDO;

/**
 * Interface RepositoryInterface
 * @package Framework\Api\Repository
 */
interface RepositoryInterface
{
    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function create(EntityInterface $entity): EntityInterface;

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function update(EntityInterface $entity): EntityInterface;

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity): EntityInterface;

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function findOne(int $id): EntityInterface;

    /**
     * @return array
     */
    public function fetchAll(): array;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOne(int $id): bool;
}
