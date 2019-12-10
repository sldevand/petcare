<?php

namespace Framework\Model\Validator;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Framework\Exception\ValidatorException;

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
            $constraints = $field['constraints'];

            $this->validateNullableField($constraints, $propertyName);
            $this->checkUniqueField($constraints, $propertyName);
            $this->checkMinLength($constraints, $propertyName);
            $this->checkMaxLength($constraints, $propertyName);
        }

        return true;
    }

    /**
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     * @throws Exception
     */
    public function validateNullableField(array $constraints, string $propertyName): bool
    {
        if (empty($constraints['nullable'])) {
            return true;
        }

        $constraintValue = $constraints['nullable'];
        $propertyMethod = $this->entity->getPropertyMethod($propertyName);
        $fieldValue = $this->entity->$propertyMethod();

        if ($constraintValue === false
            && $fieldValue === null
        ) {
            $this->throwException("$propertyName is not nullable");
        }

        return true;
    }

    /**
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     * @throws Exception
     */
    public function checkUniqueField(array $constraints, string $propertyName): bool
    {
        foreach ($constraints as $constraintKey => $constraintValue) {
            $propertyMethod = $this->entity->getPropertyMethod($propertyName);
            if (
                $constraintKey === 'unique'
                && $constraintValue === true
                && $this->entity->$propertyMethod() === null
            ) {
                $this->throwException("$propertyName must be unique");
            }
        }

        return true;
    }

    /**
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     * @throws Exception
     */
    public function checkMinLength(array $constraints, string $propertyName): bool
    {
        foreach ($constraints as $constraintKey => $constraintValue) {
            $propertyMethod = $this->entity->getPropertyMethod($propertyName);
            $propertyValue = $this->entity->$propertyMethod();
            if (
                $constraintKey === 'minLength'
                && strlen($propertyValue) < $constraintValue
            ) {
                $this->throwException("$propertyName minLength must me over $constraintValue");
            }
        }

        return true;
    }

    /**
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     * @throws Exception
     */
    public function checkMaxLength(array $constraints, string $propertyName): bool
    {
        foreach ($constraints as $constraintKey => $constraintValue) {
            $propertyMethod = $this->entity->getPropertyMethod($propertyName);
            $propertyValue = $this->entity->$propertyMethod();
            if (
                $constraintKey === 'maxLength'
                && strlen($propertyValue) > $constraintValue
            ) {
                $this->throwException("$propertyName maxLength must me under $constraintValue");
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
        throw new ValidatorException(
            "$class can't validate entity 
               $entityClass because $reason"
        );
    }
}
