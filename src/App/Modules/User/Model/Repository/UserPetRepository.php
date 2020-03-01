<?php

namespace App\Modules\User\Model\Repository;

use App\Modules\User\Model\Entity\UserPetEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class UserPetRepository
 * @package App\Modules\User\Model\Repository
 */
class UserPetRepository extends DefaultRepository
{
    /**
     * UserPetRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        parent::__construct($db, $validator);
        $this->table = "userPet";
        $this->entityClass = UserPetEntity::class;
    }

    /**
     * @param int $id
     * @return EntityInterface[]
     * @throws Exception
     */
    public function fetchAllByUserId(int $id): array
    {
        return $this->fetchAllByField('userId', $id);
    }

    /**
     * @param int $userId
     * @param int $petId
     * @return EntityInterface
     * @throws \Framework\Exception\RepositoryException
     */
    public function fetchPetByUserId(int $userId, int $petId): EntityInterface
    {
        return $this->fetchOneBy('userId', $userId, "petID = $petId");
    }
}
