<?php

namespace App\Modules\User\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class UserPetEntity
 * @package App\Modules\User\Model\Entity
 * @method getUserId()
 * @method getPetId()
 * @method setUserId($userId)
 * @method setPetId($petId)
 */
class UserPetEntity extends DefaultEntity
{
    /** @var int */
    protected $userId;

    /** @var int */
    protected $petId;

    /**
     * PetEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/userPet.yaml';
        parent::__construct($attributes);
    }
}
