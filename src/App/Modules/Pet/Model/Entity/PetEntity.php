<?php

namespace App\Modules\Pet\Model\Entity;

use Framework\Model\Entity\AbstractEntity;

/**
 * Class PetEntity
 * @package App\Model\Entity
 */
class PetEntity extends AbstractEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $dob;

    /** @var string */
    protected $specy;

    /** @var int */
    protected $imageId;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    public function __construct($attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/fields.yaml';
        parent::__construct($attributes);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => $this->dob,
            'specy' => $this->specy,
            'imageId' => $this->imageId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
