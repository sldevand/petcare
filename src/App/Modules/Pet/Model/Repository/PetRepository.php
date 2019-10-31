<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
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

    /**
     * PetRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     * @param PetImageRepository $petImageRepository
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetImageRepository $petImageRepository
    ) {
        $this->table = "pet";
        $this->entityClass = PetEntity::class;
        $this->petImageRepository = $petImageRepository;
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

        return $petEntity;
    }
}
