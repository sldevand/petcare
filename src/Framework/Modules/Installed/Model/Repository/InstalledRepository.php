<?php

namespace Framework\Modules\Installed\Model\Repository;

use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Modules\Installed\Model\Entity\InstalledEntity;
use PDO;

/**
 * Class InstalledRepository
 * @package Framework\Modules\Installed\Model\Repository
 */
class InstalledRepository extends DefaultRepository
{
    /**
     * InstalledRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator
    ) {
        parent::__construct($db, $validator);
        $this->table = "installed";
        $this->entityClass = InstalledEntity::class;
    }
}
