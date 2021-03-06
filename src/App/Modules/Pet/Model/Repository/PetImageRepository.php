<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class PetImageRepository
 * @package App\Modules\Pet\Model\Repository
 */
class PetImageRepository extends DefaultRepository
{
    /**
     * PetImageRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        parent::__construct($db, $validator);
        $this->table = "petImage";
        $this->entityClass = PetImageEntity::class;
    }
}
