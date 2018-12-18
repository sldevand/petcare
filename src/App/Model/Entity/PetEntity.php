<?php

namespace App\Model\Entity;

/**
 * Class PetEntity
 * @package App\Model\Entity
 */
class PetEntity extends AbstractEntity
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var int $age ;
     */
    protected $age;

    /**
     * @var string $specy
     */
    protected $specy;



     /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PetEntity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return PetEntity
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecy()
    {
        return $this->specy;
    }

    /**
     * @param string $specy
     * @return PetEntity
     */
    public function setSpecy($specy)
    {
        $this->specy = $specy;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'specy' => $this->specy,
            'age' => $this->age
        ];
    }
}
