<?php

namespace App\Modules\User\Model\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Entity\UserEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Traits\Repository\RepositoryTimestampableTrait;
use PDO;

/**
 * Class UserRepository
 * @package App\Modules\User\Model\Repository
 */
class UserRepository extends DefaultRepository
{
    use RepositoryTimestampableTrait;

    /** @var \App\Modules\Pet\Model\Repository\PetRepository */
    protected $petRepository;

    /**
     * UserRepository constructor.
     * @param PDO $db
     * @param \Framework\Api\Validator\ValidatorInterface $validator
     * @param \App\Modules\Pet\Model\Repository\PetRepository $petRepository
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetRepository $petRepository
    ) {
        parent::__construct($db, $validator);
        $this->table = "user";
        $this->entityClass = UserEntity::class;
        $this->petRepository = $petRepository;
    }

    /**
     * @param EntityInterface $userEntity
     * @return EntityInterface
     * @throws Exception
     */
    public function save(EntityInterface $userEntity): EntityInterface
    {
        /** @var UserEntity $user */
        $user = parent::save($userEntity);

        if (!empty($pets = $userEntity->getPets())) {
            return $this->savePets($user, $pets);
        }

        return $user;
    }

    /**
     * @param UserEntity $user
     * @param PetEntity $pet
     * @return EntityInterface
     * @throws Exception
     */
    public function savePet(UserEntity $user, PetEntity $pet): EntityInterface
    {
        $pet->setUserId($user->getId());
        $savedPet = $this->petRepository->save($pet);

        return $savedPet;
    }

    /**
     * @param int $userId
     * @return PetEntity[]
     * @throws Exception
     */
    public function fetchPets(int $userId): array
    {
        return $this->petRepository->fetchAllByField('userId', $userId);
    }

    /**
     * @param int $userId
     * @param $value
     * @param string $field
     * @return EntityInterface
     * @throws \Framework\Exception\RepositoryException
     */
    public function fetchPetBy(int $userId, $value, string $field = 'id'): EntityInterface
    {
        return $this->petRepository->fetchOneBy($field, $value, "userId=$userId");
    }

    /**
     * @param string $apiKey
     * @return \Framework\Api\Entity\EntityInterface
     * @throws \Exception
     */
    public function fetchByApiKey($apiKey): EntityInterface
    {
        return $this->fetchOneBy('apiKey', $apiKey);
    }
}
