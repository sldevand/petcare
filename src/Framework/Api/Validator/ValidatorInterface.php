<?php

namespace Framework\Api\Validator;

use Framework\Api\Entity\EntityInterface;

/**
 * Interface ValidatorInterface
 * @package Framework\Api\Validator
 */
interface ValidatorInterface
{
    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function validate(EntityInterface $entity): bool;
}
