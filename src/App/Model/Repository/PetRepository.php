<?php

namespace App\Model\Repository;

use App\Model\Entity\PetEntity;

/**
 * Class PetRepository
 * @package App\Model\Repository
 */
class PetRepository extends AbstractRepository
{
    public function __construct(\PDO $db)
    {
        $this->table = "pet_entity";
        parent::__construct($db, $this->table);
    }

    /**
     * @param PetEntity $entity
     * @return bool
     * @throws \PDOException
     */
    public function create($entity)
    {
        $sql = 'INSERT INTO ' . $this->table . ' (name,age,specy) VALUES (:name,:age,:specy)';

        $st = $this->db->prepare($sql);
        $st->bindValue(":name", $entity->getName());
        $st->bindValue(":age", $entity->getAge());
        $st->bindValue(":specy", $entity->getSpecy());

        return $st->execute();
    }

    /**
     * @param $name
     * @return \App\Model\Entity\AbstractEntity|mixed
     */
    public function findOneByName($name)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE name=:name';
        $st = $this->db->prepare($sql);
        $st->bindValue(":name", $name);
        $st->execute();

        return $st->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $sql = 'SELECT * FROM ' . $this->table;

        return $this->db->query($sql, \PDO::FETCH_ASSOC)->fetchAll();
    }
}
