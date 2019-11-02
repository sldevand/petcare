<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Pet\Model\Entity\PetCareEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class PetCareRepository
 * @package App\Modules\Pet\Model\Repository
 */
class PetCareRepository extends DefaultRepository
{
    /**
     * PetCareRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        parent::__construct($db, $validator);
        $this->table = "petCare";
        $this->entityClass = PetCareEntity::class;
    }
}
