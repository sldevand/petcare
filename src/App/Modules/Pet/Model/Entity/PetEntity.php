<?php

namespace App\Modules\Pet\Model\Entity;

use App\Modules\Care\Model\Entity\CareEntity;
use Exception;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class PetEntity
 * @package App\Model\Entity
 * @method getName
 * @method getDob
 * @method getSpecy
 * @method getImageId
 * @method getCreatedAt
 * @method setCreatedAt($date)
 * @method getUpdatedAt
 * @method setUpdatedAt($date)
 * @method getImage
 * @method setImage($image)
 */
class PetEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $dob;

    /** @var string */
    protected $specy;

    /** @var PetImageEntity */
    protected $image;

    /** @var CareEntity[] */
    protected $cares = [];

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

    /**
     * @param CareEntity $care
     * @return PetEntity
     */
    public function addCare(CareEntity $care): PetEntity
    {
        $this->cares[] = $care;

        return $this;
    }

    /**
     * @return CareEntity[]
     */
    public function getCares(): array
    {
        return $this->cares;
    }

    /**
     * @param CareEntity[] $cares
     * @return PetEntity
     */
    public function setCares(array $cares): PetEntity
    {
        $this->cares = $cares;

        return $this;
    }
}
