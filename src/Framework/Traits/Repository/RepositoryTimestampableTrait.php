<?php

namespace Framework\Traits\Repository;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Helper\DateHelper;

/**
 * Class RepositoryTimestampableTrait
 * @package Framework\Traits\Repository
 */
trait RepositoryTimestampableTrait
{
    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function create(EntityInterface $entity): EntityInterface
    {
        $entity->setCreatedAt(DateHelper::now());
        return parent::create($entity);
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $entity->setUpdatedAt(DateHelper::now());
        return parent::update($entity);
    }
}
