<?php

namespace App\Modules\PasswordReset\Model\Repository;

use App\Modules\PasswordReset\Model\Entity\PasswordResetEntity;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class PasswordResetRepository
 * @package App\Modules\PasswordReset\Model\Repository
 */
class PasswordResetRepository extends DefaultRepository
{
    /**
     * PasswordResetRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator
    ) {
        parent::__construct($db, $validator);
        $this->table = "passwordReset";
        $this->entityClass = PasswordResetEntity::class;
    }
}
