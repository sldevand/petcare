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
     * RepositoryInterface constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator);

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function create(EntityInterface $entity): bool;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function update(EntityInterface $entity): bool;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool;

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
