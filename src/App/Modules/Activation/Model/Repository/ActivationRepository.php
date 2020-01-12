<?php

namespace App\Modules\Activation\Model\Repository;

use App\Modules\Activation\Model\Entity\ActivationEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Traits\Repository\RepositoryTimestampableTrait;
use PDO;

/**
 * Class ActivationRepository
 * @package App\Modules\Activation\Model\Repository
 */
class ActivationRepository extends DefaultRepository
{
    use RepositoryTimestampableTrait;

    /**
     * ActivationRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator
    ) {
        parent::__construct($db, $validator);
        $this->table = "activation";
        $this->entityClass = ActivationEntity::class;
    }
}
