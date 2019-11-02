<?php

namespace App\Modules\Care\Model\Repository;

use App\Modules\Care\Model\Entity\CareEntity;
use App\Modules\Pet\Model\Entity\PetImageEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class CareRepository
 * @package App\Modules\Care\Model\Repository
 */
class CareRepository extends DefaultRepository
{
    /**
     * CareRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        parent::__construct($db, $validator);
        $this->table = "care";
        $this->entityClass = CareEntity::class;
    }
}
