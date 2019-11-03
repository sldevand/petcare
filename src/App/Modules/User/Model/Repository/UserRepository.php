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
use PDO;

/**
 * Class UserRepository
 * @package App\Modules\User\Model\Repository
 */
class UserRepository extends DefaultRepository
{
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
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        $user = parent::save($entity);

        if (!empty($pets = $entity->getPets())) {
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
            $pet = $this->petRepository->save($pet);
            $user->addPet($pet);
            $userPetEntity = new UserPetEntity();
            $userPetEntity
                ->setUserId($user->getId())
                ->setPetId($pet->getId());

            $this->userPetRepository->create($userPetEntity);
        }

        return $user;
    }

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
}
