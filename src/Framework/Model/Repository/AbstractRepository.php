<?php

namespace Framework\Model\Repository;

use Exception;
use Framework\Api\EntityInterface;
use Framework\Api\ValidatorInterface;
use Framework\Model\Entity\DefaultEntity;
use PDO;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository
{
    /** @var PDO */
    protected $db;

    /** @var string */
    protected $table;

    /** @var string */
    protected $entityClass;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * AbstractRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        $this->db = $db;
        $this->validator = $validator;
    }

    /**
     * @param DefaultEntity $entity
     * @return bool
     * @throws Exception
     */
    public function create($entity)
    {
        $this->validator->validate($entity);
        $sql = $this->prepareInsertSql($entity);
        $st = $this->db->prepare($sql);

        foreach ($entity->getFields() as $property => $field) {
            if (!empty($entity->__get($property))) {
                $st->bindValue($property, $entity->__get($property));
            }
        }

        return $st->execute();
    }

    /**
     * @param DefaultEntity $entity
     * @return bool
     * @throws Exception
     */
    public function update($entity)
    {
        $this->validator->validate($entity);
        $sql = $this->prepareUpdateSql($entity);
        $st = $this->db->prepare($sql);
        $st->bindValue(':id', $entity->getId());
        foreach ($entity->getFields() as $property => $field) {
            if (!empty($entity->__get($property))) {
                $st->bindValue($property, $entity->__get($property));
            }
        }

        return $st->execute();
    }

    /**
     * @param DefaultEntity $entity
     * @return bool|mixed
     * @throws Exception
     */
    public function save($entity)
    {
        if (!$entity->getId()) {
            return $this->create($entity);
        }

        return $this->update($entity);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOne($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id=:id";
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);


        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();


        return $st->fetch();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function findOneByName($name)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE name=:name';
        $st = $this->db->prepare($sql);
        $st->bindValue(":name", $name);
        $st->execute();


        $st->setFetchMode(PDO::FETCH_CLASS, $this->entityClass, ['attributes' => []]);

        return $st->fetch($this->entityClass);
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
     * @return bool
     */
    public function deleteOne($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);

        return $st->execute();
    }

    /**
     * @param DefaultEntity $entity
     * @return string
     * @throws Exception
     */
    protected function prepareInsertSql($entity)
    {
        $fieldsPart = '';
        $valuesPart = '';
        $fields = $entity->getFields();

        $iter = 1;
        foreach ($fields as $property => $field) {
            $value = $entity->__get($property);
            $iter++;
            if (is_null($value)) {
                continue;
            }

            $fieldsPart .= $field['column'];
            $valuesPart .= ':' . $property;
            if ($iter < count($fields)) {
                $fieldsPart .= ',';
                $valuesPart .= ',';
            }
        }

        return <<<SQL
INSERT INTO $this->table ($fieldsPart) VALUES ($valuesPart);
SQL;
    }

    /**
     * @param DefaultEntity $entity
     * @return string
     * @throws Exception
     */
    protected function prepareUpdateSql($entity)
    {
        $fieldsPart = '';
        $fields = $entity->getFields();

        $iter = 1;
        foreach ($fields as $property => $field) {
            $value = $entity->__get($property);
            $iter++;
            if (is_null($value)) {
                continue;
            }

            $fieldsPart .= $field['column'] . ' = :' . $property;

            if ($iter < count($fields)) {
                $fieldsPart .= ',';
            }
        }

        return <<<SQL
UPDATE $this->table SET $fieldsPart WHERE id = :id;
SQL;
    }
}
