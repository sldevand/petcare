<?php

namespace App\Modules\Notification\Model\Repository;

use App\Modules\Notification\Model\Entity\NotificationEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Traits\Repository\RepositoryTimestampableTrait;
use PDO;

/**
 * Class NotificationRepository
 * @package App\Modules\Notification\Model\Repository
 */
class NotificationRepository extends DefaultRepository
{
    use RepositoryTimestampableTrait;

    /**
     * NotificationRepository constructor.
     * @param \PDO $db
     * @param \Framework\Api\Validator\ValidatorInterface $validator
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator
    ) {
        parent::__construct($db, $validator);
        $this->table = "notifcation";
        $this->entityClass = NotificationEntity::class;
    }
}
