<?php

namespace App\Model\Repository;

use App\Model\Entity\AbstractEntity;
use App\Model\Entity\PetEntity;
use PDO;

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
     * @var array $fields
     */
    protected $fields;

    /**
     * @var string $entityClass
     */
    protected $entityClass;

    /**
     * PetRepository constructor.
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param AbstractEntity $entity
     * @return mixed
     */
    public function create($entity)
    {
        $entity = $entity->jsonSerialize();

        $sql = 'INSERT INTO ' . $this->table . ' (';
        $values = ') VALUES (';

        foreach ($this->fields as $key => $field) {
            $sql .= $field;
            $values .= '?';

            if ($key < count($this->fields) - 1) {
                $sql .= ',';
                $values .= ',';
            }
        }
        $sql .= $values . ');';
        $st = $this->db->prepare($sql);

        foreach ($this->fields as $key => $field) {
            $st->bindValue($key + 1, $entity[$field]);
        }

        return $st->execute();
    }

    /**
     * @param AbstractEntity $entity
     * @return bool
     */
    public function update($entity)
    {
        if (!$entity->getId()) {
            return false;
        }

        $entity = $entity->jsonSerialize();

        $sql = 'UPDATE ' . $this->table . ' SET ';
        foreach ($this->fields as $key => $field) {
            $sql .= $field . '=?';

            if ($key < count($this->fields) - 1) {
                $sql .= ',';
            }
        }
        $sql .= ' WHERE id=' . $entity['id'];

        $st = $this->db->prepare($sql);

        foreach ($this->fields as $key => $field) {
            $st->bindValue($key + 1, $entity[$field]);
        }

        return $st->execute();
    }

    /**
     * @param AbstractEntity $entity
     * @return bool|mixed
     */
    public function save($entity){

        if (!$entity->getId()) {
            return $this->create($entity);
        }

        return $this->update($entity);
    }

    /**
     * @param $id
     *
     * @return \App\Model\Entity\AbstractEntity
     */
    public function findOne($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);
        $st->execute();

        return $st->fetchObject($this->entityClass);
    }

    /**
     * @param $name
     * @return \App\Model\Entity\AbstractEntity
     */
    public function findOneByName($name)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE name=:name';
        $st = $this->db->prepare($sql);
        $st->bindValue(":name", $name);
        $st->execute();

        return $st->fetchObject($this->entityClass);
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $sql = 'SELECT * FROM ' . $this->table;

        return $this->db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function deleteOne($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);

        return $st->execute();
    }
}
