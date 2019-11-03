<?php

namespace Framework\Modules\Module\Model\Repository;

use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use Framework\Modules\Module\Model\Entity\ModuleEntity;
use PDO;

/**
 * Class ModuleRepository
 * @package Framework\Modules\Module\Model\Repository
 */
class ModuleRepository extends DefaultRepository
{
    public function __construct(
        PDO $db,
        ValidatorInterface $validator
    ) {
        parent::__construct($db, $validator);
        $this->table = "module";
        $this->entityClass = ModuleEntity::class;
    }
}
