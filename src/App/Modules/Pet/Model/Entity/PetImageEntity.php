<?php

namespace App\Modules\Pet\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class PetImageEntity
 * @package App\Modules\Pet\Model\Entity
 */
class PetImageEntity extends DefaultEntity
{
    /** @var string */
    protected $petId;

    /** @var string */
    protected $image;

    /** @var string */
    protected $thumbnail;

    /**
     * PetImageEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/petImage.yaml';
        parent::__construct($attributes);
    }
}
