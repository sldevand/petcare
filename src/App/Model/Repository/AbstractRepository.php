<?php

namespace App\Model\Repository;

use App\Model\Entity\AbstractEntity;
use App\Model\Entity\EntityInterface;
use App\Model\Entity\PetEntity;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository
{
    /**
     * @var \PDO $db
     */
    protected $db;


    /**
     * @var string $table
     */
    protected $table;

    /**
     * PetRepository constructor.
     * @param \PDO $db
     * @param $table
     */
    public function __construct(\PDO $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * @param $entity
     * @return mixed
     */
    abstract public function create($entity);

    /**
     * @return array
     */
    public function findAll()
    {
        return [];
    }

    /**
     * @param $id
     * @return AbstractEntity
     */
    public function findOne($id)
    {
        return new PetEntity("elie", 5, 'cat', $id);
    }

    /**
     * @param $name
     * @return AbstractEntity
     */
    public function findOneByName($name)
    {
        return new PetEntity($name, 5, 'cat', 2);
    }
}
