<?php

namespace App\Modules\User\Model\Repository;

use App\Modules\Activation\Model\Repository\NotificationRepository;
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

    /** @var \App\Modules\Activation\Model\Repository\NotificationRepository */
    protected $activationRepository;

    /**
     * UserRepository constructor.
     * @param PDO $db
     * @param \Framework\Api\Validator\ValidatorInterface $validator
     * @param \App\Modules\Pet\Model\Repository\PetRepository $petRepository
     * @param \App\Modules\Activation\Model\Repository\NotificationRepository $activationRepository
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetRepository $petRepository,
        NotificationRepository $activationRepository
    ) {
        parent::__construct($db, $validator);
        $this->table = "user";
        $this->entityClass = UserEntity::class;
        $this->petRepository = $petRepository;
        $this->activationRepository = $activationRepository;
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

    /**
     * @param int $minutes
     * @return array
     * @throws \Exception
     */
    public function purgeNeverActivatedUsers(int $minutes)
    {
        $deletedUserIds = [];
        if ($activations = $this->activationRepository->getNotActivatedForMinutes($minutes)) {
            foreach ($activations as $activation) {
                $this->deleteOne($activation->getUserId());
                $deletedUserIds[] = $activation->getUserId();
            }
        }

        return $deletedUserIds;
    }
}
