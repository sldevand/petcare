<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Exception\RepositoryException;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class PetRepository
 * @package App\Modules\Pet\Model\Repository
 */
class PetRepository extends DefaultRepository
{
    /** @var PetImageRepository */
    protected $petImageRepository;

    /** @var PetCareRepository */
    protected $petCareRepository;

    /**
     * PetRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     * @param PetImageRepository $petImageRepository
     * @param PetCareRepository $petCareRepository
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetImageRepository $petImageRepository,
        PetCareRepository $petCareRepository
    ) {
        $this->table = "pet";
        $this->entityClass = PetEntity::class;
        $this->petImageRepository = $petImageRepository;
        $this->petCareRepository = $petCareRepository;
        parent::__construct($db, $validator);
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws Exception
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        $petEntity = parent::save($entity);

        if (!empty($image = $entity->getImage())) {
            $image->setPetId($petEntity->getId());
            $petImageEntity = $this->petImageRepository->save($image);
            $petEntity->setImage($petImageEntity);
        }

        if (!empty($cares = $entity->getCares())) {
            foreach ($cares as $care) {
                $care->setPetId($petEntity->getId());
                $this->petCareRepository->save($care);
            }
        }

        return $petEntity;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function fetchImage(EntityInterface $entity): EntityInterface
    {
        $image = $this->petImageRepository->findOneBy('petId', $entity->getId());
        $entity->setImage($image);

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function fetchCares(EntityInterface $entity): EntityInterface
    {
        $image = $this->petCareRepository->findAll('petId', $entity->getId());
        $entity->setImage($image);

        return $entity;
    }
}
