<?php

namespace App\Modules\Pet\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class PetEntity
 * @package App\Model\Entity
 * @method getName
 * @method getDob
 * @method getSpecy
 * @method getImageId
 * @method getCreatedAt
 * @method getUpdatedAt
 * @method getImage
 */
class PetEntity extends DefaultEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $dob;

    /** @var string */
    protected $specy;

    /** @var int */
    protected $imageId;

    /** @var PetImageEntity */
    protected $image;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    /**
     * PetEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/pet.yaml';
        parent::__construct($attributes);
    }
}
