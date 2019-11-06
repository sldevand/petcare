<?php

namespace Framework\Model\Repository;

use DateTime;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Exception\RepositoryException;
use Framework\MagicObject;
use PDO;
use PDOStatement;

/**
 * Class DefaultRepository
 * @package Framework\Model\Repository
 */
class DefaultRepository extends MagicObject implements RepositoryInterface
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
     * DefaultRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     */
    public function __construct(PDO $db, ValidatorInterface $validator)
    {
        $this->db = $db;
        $this->validator = $validator;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function create(EntityInterface $entity): EntityInterface
    {
        $this->validator->validate($entity);
        $sql = $this->prepareInsertSql($entity);
        $st = $this->prepare($sql);

        foreach ($entity->getFields() as $property => $field) {
            $getPropertyMethod = $this->getPropertyMethod($property);
            if (!empty($entity->$getPropertyMethod()) && $property !== 'id') {
                $st->bindValue($property, $entity->$getPropertyMethod());
            }
        }
        $st->execute();
        $st->closeCursor();

        $id = $this->getLastInserted($this->table);

        return $this->fetchOne($id);
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $this->validator->validate($entity);
        $sql = $this->prepareUpdateSql($entity);
        $st = $this->prepare($sql);
        $st->bindValue(':id', $entity->getId());
        foreach ($entity->getFields() as $property => $field) {
            $getPropertyMethod = $this->getPropertyMethod($property);
            if (!empty($entity->$getPropertyMethod())) {
                $st->bindValue(':' . $property, $entity->$getPropertyMethod());
            }
        }

        $st->execute();
        $st->closeCursor();

        return $this->fetchOne($entity->getId());
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        if (empty($entity->getId())) {
            return $this->create($entity);
        }

        return $this->update($entity);
    }

    /**
     * @param int $id
     * @return EntityInterface
     * @throws RepositoryException
     * @throws Exception
     */
    public function fetchOne(int $id): EntityInterface
    {
        return $this->fetchOneBy('id', $id);
    }

    /**
     * @param string $field
     * @param int $value
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function fetchOneBy(string $field, int $value): EntityInterface
    {
        $sql = "SELECT * FROM $this->table WHERE $field=:$field";
        $st = $this->prepare($sql);
        $st->bindValue(":$field", $value);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();

        if (!$result = $st->fetch()) {
            $class = get_class($this);
            throw new RepositoryException("$class::fetchOneBy($field, $value) --> cannot fetch: PDO error");
        }

        return $result;
    }

    /**
     * @return EntityInterface[]
     * @throws Exception
     */
    public function fetchAll(): array
    {
        $sql = "SELECT * FROM $this->table";
        $st = $this->prepare($sql);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();

        return $st->fetchAll();
    }

    /**
     * @param string $field
     * @param int $value
     * @return EntityInterface[]
     * @throws Exception
     */
    public function fetchAllByField(string $field, int $value): array
    {
        $sql = "SELECT * FROM $this->table WHERE $field=:$field";

        $st = $this->prepare($sql);
        $st->bindValue(":$field", $value);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();

        return $st->fetchAll();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOne(int $id): bool
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id=:id';
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);

        return $st->execute();
    }

    /**
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    public function getLastInserted($table)
    {
        $sql = 'SELECT seq FROM sqlite_sequence WHERE name="' . $table . '"';
        $q = $this->query($sql);
        $q->execute();
        $res = $q->fetchColumn();

        return $res;
    }

    /**
     * @param string $sql
     * @return PDOStatement
     * @throws Exception
     */
    public function prepare($sql)
    {
        $query = $this->db->prepare($sql);
        if (!$query) {
            throw new Exception(implode(" ", $this->db->errorInfo()));
        }

        return $query;
    }

    /**
     * @param string $sql
     * @return PDOStatement
     * @throws Exception
     */
    public function query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            throw new Exception(implode(" ", $this->db->errorInfo()));
        }

        return $query;
    }

    /**
     * @param EntityInterface $entity
     * @return string
     * @throws Exception
     */
    protected function prepareInsertSql(EntityInterface $entity): string
    {
        $columns = $columns = $this->prepareColumns($entity);

        $columnsSql = implode(',', array_keys($columns));
        $valuesSql = implode(',', $columns);

        return <<<SQL
INSERT INTO $this->table ($columnsSql) VALUES ($valuesSql);
SQL;
    }

    /**
     * @param EntityInterface $entity
     * @return string
     * @throws Exception
     */
    protected function prepareUpdateSql(EntityInterface $entity): string
    {
        $columns = $this->prepareColumns($entity);

        $columnsArr = [];
        foreach ($columns as $key => $value) {
            $columnsArr[] = $key . ' = ' . $value;
        }

        $columnsSql = implode(',', $columnsArr);

        return <<<SQL
UPDATE $this->table SET $columnsSql WHERE id = :id;
SQL;
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    protected function prepareColumns(EntityInterface $entity): array
    {
        $columns = [];
        foreach ($entity->getFields() as $property => $field) {
            if ($property === 'id') {
                continue;
            }

            $propertyMethod = $entity->getPropertyMethod($property);
            $value = $entity->$propertyMethod();
            if (is_null($value)) {
                continue;
            }

            $columns[$field['column']] = ':' . $property;
        }

        return $columns;
    }
}
