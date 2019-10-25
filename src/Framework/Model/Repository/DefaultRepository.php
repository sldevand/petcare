<?php

namespace Framework\Model\Repository;

use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Exception\RepositoryException;
use Framework\MagicObject;
use PDO;

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
     * @return bool
     * @throws Exception
     */
    public function create(EntityInterface $entity): bool
    {
        $this->validator->validate($entity);
        $sql = $this->prepareInsertSql($entity);
        $st = $this->db->prepare($sql);

        foreach ($entity->getFields() as $property => $field) {
            $getPropertyMethod = $this->getPropertyMethod($property);
            if (!empty($entity->$getPropertyMethod())) {
                $st->bindValue($property, $entity->$getPropertyMethod());
            }
        }

        return $st->execute();
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     * @throws Exception
     */
    public function update(EntityInterface $entity): bool
    {
        $this->validator->validate($entity);
        $sql = $this->prepareUpdateSql($entity);
        $st = $this->db->prepare($sql);
        $st->bindValue(':id', $entity->getId());
        foreach ($entity->getFields() as $property => $field) {
            $getPropertyMethod = $this->getPropertyMethod($property);
            if (!empty($entity->$getPropertyMethod()) && $property !== 'id') {
                $st->bindValue(':' . $property, $entity->$getPropertyMethod());
            }
        }

        return $st->execute();
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     * @throws Exception
     */
    public function save(EntityInterface $entity): bool
    {
        if (!$entity->getId()) {
            return $this->create($entity);
        }

        return $this->update($entity);
    }

    /**
     * @param int $id
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function findOne(int $id): EntityInterface
    {
        $sql = "SELECT * FROM $this->table WHERE id=:id";
        $st = $this->db->prepare($sql);
        $st->bindValue(":id", $id);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityClass);
        $st->execute();

        if (!$result = $st->fetch()) {
            $class = get_class($this);
            throw new RepositoryException("$class::findOne --> cannot fetch: PDO error");
        }

        return $result;
    }

    /**
     * @return array
     */
    public function fetchAll(): array
    {
        $sql = 'SELECT * FROM ' . $this->table;

        return $this->db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
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
     * @param EntityInterface $entity
     * @return string
     * @throws Exception
     */
    protected function prepareInsertSql(EntityInterface $entity): string
    {
        $fieldsPart = '';
        $valuesPart = '';
        $fields = $entity->getFields();

        $iter = 2;
        foreach ($fields as $property => $field) {
            $iter++;
            if ($property === 'id') {
                continue;
            }

            $propertyMethod = $entity->getPropertyMethod($property);
            $value = $entity->$propertyMethod();
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
     * @param EntityInterface $entity
     * @return string
     * @throws Exception
     */
    protected function prepareUpdateSql(EntityInterface $entity): string
    {
        $fieldsPart = '';
        $fields = $entity->getFields();

        $iter = 3;
        foreach ($fields as $property => $field) {
            $iter++;
            if ($property === 'id') {
                continue;
            }

            $propertyMethod = $entity->getPropertyMethod($property);
            $value = $entity->$propertyMethod();
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
