<?php

namespace App\Modules\User\Model\Entity;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class UserEntity
 * @package App\Modules\User\Model\Entity
 */
class UserEntity extends DefaultEntity
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    /** @var PetEntity[] */
    protected $pets;

    /**
     * PetEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/user.yaml';
        parent::__construct($attributes);
    }

    public function addPet(PetEntity $pet): UserEntity
    {
        $this->pets[$pet->getId()] = $pet;

        return $this;
    }
}