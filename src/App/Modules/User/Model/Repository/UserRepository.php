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

    /** @var UserPetRepository */
    protected $userPetRepository;

    /** @var PetRepository */
    protected $petRepository;

    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        UserPetRepository $userPetRepository,
        PetRepository $petRepository
    ) {
        parent::__construct($db, $validator);
        $this->table = "user";
        $this->entityClass = UserEntity::class;
        $this->userPetRepository = $userPetRepository;
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
        $savedPet = $this->petRepository->save($pet);
        $userPetEntity = new UserPetEntity();
        $userPetEntity
            ->setUserId($user->getId())
            ->setPetId($savedPet->getId());

        try{
            $this->userPetRepository->fetchPetByUserId($user->getId(), $savedPet->getId());
        } catch (Exception $exception){
            $this->userPetRepository->create($userPetEntity);
        }

        return $savedPet;
    }

    /**
     * @return array
     * @throws \Framework\Exception\RepositoryException
     * @throws \Exception
     */
    public function fetchAll(): array
    {
        $users = parent::fetchAll();

        foreach ($users as $userKey => $user) {
            $userPets = $this->userPetRepository->fetchAllByUserId($user->getId());
            foreach ($userPets as $userPetKey => $userPet) {
                $pet = $this->petRepository->fetchOne($userPet->getPetId());
                $users[$userKey]->addPet($pet);
            }
        }

        return $users;
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
     * @param int $petId
     * @return EntityInterface
     * @throws \Framework\Exception\RepositoryException
     * @throws Exception
     */
    public function fetchPet(int $userId, int $petId): EntityInterface
    {
        $userPet = $this->userPetRepository->fetchPetByUserId($userId, $petId);
        if (empty($userPet)) {
            throw new Exception("No userPet found with this $petId");
        }

        return $this->petRepository->fetchOne($petId);
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
