<?php

namespace App\Model\Entity;

/**
 * Class AbstractEntity
 * @package App\Model\Entity
 */
abstract class AbstractEntity implements EntityInterface
{
    /** @var int $id */
    protected $id;

    /**
     * AbstractEntity constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        if (!empty($attributes)) {
            $this->hydrate($attributes);
        }
    }

    /**
     * @param array $attributes
     */
    public function hydrate($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribute)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AbstractEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
