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
    public function fetchAllByUserId($id): array
    {
        return $this->fetchAllByField('userId', $id);
    }
}
