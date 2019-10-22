<?php

namespace Framework\Model\Validator;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Class DefaultValidator
 * @package Framework\Model\Validator
 */
class DefaultValidator implements ValidatorInterface
{
    /** @var EntityInterface */
    protected $entity;

    /**
     * @param EntityInterface $entity
     * @return bool.
     * @throws ParseException
     * @throws Exception
     */
    public function validate(EntityInterface $entity): bool
    {
        $this->entity = $entity;
        $fields = $entity->getFields();

        foreach ($fields as $propertyName => $field) {
            if (empty($field['constraints'])) {
                continue;
            }
            $this->checkNullableField($field['constraints'], $propertyName);
        }

        return true;
    }

    /**
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     * @throws Exception
     */
    public function checkNullableField(array $constraints, string $propertyName): bool
    {
        foreach ($constraints as $constraintKey => $constraintValue) {
            if (
                $constraintKey === 'nullable'
                && $constraintValue === false
                && $this->entity->__get($propertyName) === null
            ) {
                $this->throwException("$propertyName is not nullable");
            }
        }

        return true;
    }

    /**
     * @param string $reason
     * @throws Exception
     */
    protected function throwException(string $reason)
    {
        $class = get_class($this);
        $entityClass = get_class($this->entity);
        throw new \Exception(
            "$class can't validate entity 
               $entityClass because $reason"
        );
    }
}
