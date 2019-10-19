<?php

namespace Framework\Model\Validator;

use Exception;
use Framework\Api\EntityInterface;
use Framework\Api\ValidatorInterface;
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
     * @return bool|void.
     * @throws ParseException
     * @throws Exception
     */
    public function validate($entity)
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
    public function checkNullableField($constraints, $propertyName)
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
    protected function throwException($reason)
    {
        $class = get_class($this);
        $entityClass = get_class($this->entity);
        throw new \Exception(
            "$class can't validate entity 
               $entityClass because $reason"
        );
    }
}
