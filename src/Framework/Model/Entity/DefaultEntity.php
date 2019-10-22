<?php

namespace Framework\Model\Entity;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultEntity
 * @package Framework\Model\Entity
 */
class DefaultEntity implements EntityInterface
{
    /** @var int $id */
    protected $id;

    /** @var string */
    protected $configFile;

    /** @var array */
    protected $fields;

    /**
     * AbstractEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        if (!empty($attributes)) {
            $this->hydrate($attributes);
        }
        $this->fields = $this->getFieldsFromFile();
    }

    /**
     * @param array $attributes
     * @throws Exception
     */
    public function hydrate(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->__set($attribute, $value);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getFieldsFromFile(): array
    {
        $parsedFile = Yaml::parseFile($this->configFile);

        $class = get_class($this);
        if (empty($parsedFile['fields'])) {
            throw new Exception(
                "$class::getFieldsFromFile --> no config has been set in $this- file !"
            );
        }

        return Yaml::parseFile($this->configFile)['fields'];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name)
    {
        $this->hasProperty($name);

        return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return EntityInterface
     * @throws Exception
     */
    public function __set(string $name, $value): EntityInterface
    {
        $this->hasProperty($name);
        $this->$name = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function hasProperty(string $name): bool
    {
        $class = get_class($this);
        if (!property_exists($class, $name)) {
            throw new Exception(
                "$class::propertyExist --> The property $name does not exist!"
            );
        }

        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize(): array
    {
        $properties = get_class_vars(get_class($this));
        $serialized = [];
        foreach ($properties as $property => $value) {
            if ($property === 'configFile' || $property === 'fields') {
                continue;
            }
            $serialized[$property] = $this->__get($property);
        }

        return $serialized;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function setId(int $id): EntityInterface
    {
        $this->id = $id;

        return $this;
    }
}
