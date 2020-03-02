<?php

namespace App\Modules\User\Model\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\User\Model\Entity\UserEntity;
use App\Modules\User\Model\Entity\UserPetEntity;
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
     * @param PetEntity[] $pets
     * @return UserEntity
     * @throws Exception
     */
    public function savePets(UserEntity $user, array $pets): UserEntity
    {
        foreach ($pets as $pet) {
            $savedPet = $this->savePet($user, $pet);
            $user->addPet($savedPet);
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
     * @throws \Framework\Exception\RepositoryException
     */
    public function fetchPets(int $userId): array
    {
        $userPets = $this->userPetRepository->fetchAllByUserId($userId);
        $pets = [];
        foreach ($userPets as $userPetKey => $userPet) {
            $pets[$userPet->getId()] = $this->petRepository->fetchOne($userPet->getPetId());
        }

        return $pets;
    }

    /**
     * @param int $userId
     * @param $value
     * @param string $field
     * @return EntityInterface
     * @throws \Framework\Exception\RepositoryException
     * @throws Exception
     */
    public function fetchPet(int $userId, $value, string $field = 'id'): EntityInterface
    {
        $pet = $this->petRepository->fetchOneBy($field, $value);
        $userPet = $this->userPetRepository->fetchPetByUserId($userId, $pet->getId());
        if (empty($userPet)) {
            throw new Exception("No userPet found with this " . $pet->getId());
        }

        return $pet;
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
