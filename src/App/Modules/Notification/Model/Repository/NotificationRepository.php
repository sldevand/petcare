<?php

namespace App\Modules\Notification\Model\Repository;

use App\Modules\Notification\Model\Entity\NotificationEntity;
use Framework\Api\Entity\EntityInterface;
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
        $this->table = "notification";
        $this->entityClass = NotificationEntity::class;
    }

    public function save(EntityInterface $entity): EntityInterface
    {
        if (empty($entity->getId())) {
            try {
                return $this->create($entity);
            } catch (\Exception $e) {
                $notification = $this->fetchOneBy('careId', $entity->getCareId());
                $entity->setId($notification->getId());
                return $this->update($entity);
            }
        }

        return $this->update($entity);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getNotifiableUsers()
    {
        $sql = <<<SQL
SELECT user.id AS userId,
       user.firstName AS userFirstname,
       user.lastName AS userLastname,
       user.email AS userEmail,
       p.id AS petId,
       p.name AS petName,
       c.id AS careId,
       c.title AS careTitle,
       c.appointmentDate,
       strftime('%s', c.appointmentDate) - strftime('%s', 'now', 'localtime') AS diff
FROM user
       LEFT JOIN pet p on user.id = p.userId
       LEFT JOIN care c on p.id = c.petId
       LEFT JOIN notification ON c.id = notification.careId
WHERE diff > 0
  AND notification.sent ISNULL;
SQL;
        $st = $this->prepare($sql);
        $st->setFetchMode(PDO::FETCH_ASSOC | PDO::FETCH_PROPS_LATE);
        $st->execute();

        return $st->fetchAll();
    }
}
