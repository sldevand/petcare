<?php

namespace Framework\Model\Entity;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\MagicObject;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultEntity
 * @package Framework\Model\Entity
 */
class DefaultEntity extends MagicObject implements EntityInterface
{
    /** @var int $id */
    protected $id;

    /** @var string */
    protected $configFile;

    /** @var array */
    protected $fields;

    /**
     * AbstractEntity constructor.
     * @param array $properties
     * @throws Exception
     */
    public function __construct(array $properties = [])
    {
        if (!empty($properties)) {
            $this->hydrate($properties);
        }
        $this->fields = $this->getFieldsFromFile();
    }

    /**
     * @param array $properties
     * @throws Exception
     */
    public function hydrate(array $properties)
    {
        foreach ($properties as $property => $value) {
            $setMethod = $this->setPropertyMethod($property);
            $this->$setMethod($value);
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
            $getMethod = $this->getPropertyMethod($property);
            $serialized[$property] = $this->$getMethod;
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
     * @return int | null
     */
    public function getId(): ?int
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
