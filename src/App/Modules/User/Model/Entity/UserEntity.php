<?php

namespace App\Modules\User\Model\Entity;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class UserEntity
 * @package App\Modules\User\Model\Entity
 */
class UserEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var string */
    protected $apiKey;

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

    /**
     * @param EntityInterface $pet
     * @return UserEntity
     */
    public function addPet(EntityInterface $pet): UserEntity
    {
        $this->pets[] = $pet;

        return $this;
    }
}
