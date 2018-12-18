<?php

namespace App\Model\Repository;

use App\Model\Entity\PetEntity;

/**
 * Class PetRepository
 * @package App\Model\Repository
 */
class PetRepository extends AbstractRepository
{
    /**
     * PetRepository constructor.
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->table = "pet_entity";
        $this->fields = [
            'name',
            'age',
            'specy'
        ];
        $this->entityClass = PetEntity::class;
    }
}
