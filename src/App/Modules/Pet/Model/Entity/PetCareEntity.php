<?php

namespace App\Modules\Pet\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class PetCareEntity
 * @package App\Modules\Pet\Model\Entity
 */
class PetCareEntity extends DefaultEntity
{
    /** @var string */
    protected $petId;

    /** @var string */
    protected $careId;

    /**
     * PetCareEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/petCare.yaml';
        parent::__construct($attributes);
    }
}
