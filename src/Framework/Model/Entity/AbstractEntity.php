<?php

namespace Framework\Model\Entity;

use Exception;
use Framework\Api\EntityInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AbstractEntity
 * @package App\Model\Entity
 */
abstract class AbstractEntity implements EntityInterface
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
    public function __construct($attributes = [])
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
    public function hydrate($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->__set($attribute, $value);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getFieldsFromFile()
    {
        $parsedFile = Yaml::parseFile($this->configFile);

        $class = get_class($this);
        if (empty($parsedFile['fields'])) {
            throw new Exception(
                "$class::getFieldsFromFile --> no config has been set in schema.yaml file !"
            );
        }

        return Yaml::parseFile($this->configFile)['fields'];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $this->propertyExist($name);
        return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $this->propertyExist($name);
        $this->$name = $value;

        return $this;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    protected function propertyExist($name)
    {
        $class = get_class($this);
        if (!property_exists($class, $name)) {
            throw new Exception(
                "$class::propertyExist --> The property $name does not exist!"
            );
        }
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize()
    {
        return [];
    }

    /**
     * @param int $id
     * @return AbstractEntity
     */
    public function setId(int $id): AbstractEntity
    {
        $this->id = $id;

        return $this;
    }
}
