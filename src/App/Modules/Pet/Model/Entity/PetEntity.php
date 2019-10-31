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

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    /** @var PetImageEntity */
    protected $image;

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
