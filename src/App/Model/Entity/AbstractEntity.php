<?php

namespace App\Model\Entity;

/**
 * Class AbstractEntity
 * @package App\Model\Entity
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var int $id
     */
    protected $id;

    /**
     * AbstractEntity constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
