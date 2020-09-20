<?php

namespace App\Modules\Notification\Model\Repository;

use App\Modules\Notification\Model\Entity\NotificationEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Traits\Repository\RepositoryTimestampableTrait;
use PDO;

/**
 * Class NotificationRepository
 * @package App\Modules\Activation\Model\Repository
 */
class NotificationRepository extends DefaultRepository
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
        $this->table = "notifcation";
        $this->entityClass = NotificationEntity::class;
    }

    /**
     * @param int $minutes
     * @return mixed
     * @throws \Exception
     */
    public function getNotActivatedForMinutes(int $minutes)
    {
        $sql = <<<SQL
SELECT userId, strftime('%s','now', 'localtime') - strftime('%s', createdAt) AS diff 
FROM activation 
WHERE activated IS NULL 
AND diff > $minutes*60;
SQL;
        $st = $this->prepare($sql);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();

        return $st->fetchAll();
    }
}
