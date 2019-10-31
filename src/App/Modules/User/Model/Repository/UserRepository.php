<?php

namespace App\Modules\User\Model\Repository;

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

    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        UserPetRepository $userPetRepository
    )
    {
        parent::__construct($db, $validator);
        $this->table = "user";
        $this->entityClass = UserEntity::class;
        $this->userPetRepository = $userPetRepository;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        $user = parent::save($entity);
        $userId = $user->getId();

        if (!empty($pets = $entity->getPets())) {
            $user->setPets($pets);
            foreach ($pets as $pet) {
                $userPetEntity = new UserPetEntity();
                $userPetEntity
                    ->setUserId($userId)
                    ->setPetId($pet->getId());

                $this->userPetRepository->create($userPetEntity);
            }
        }

        return $user;
    }
}
