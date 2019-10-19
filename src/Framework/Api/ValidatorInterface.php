<?php

namespace Framework\Api;

/**
 * Interface ValidatorInterface
 * @package Framework\Api
 */
interface ValidatorInterface
{
    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function validate($entity);
}
