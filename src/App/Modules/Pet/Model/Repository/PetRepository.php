<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Pet\Model\Entity\PetEntity;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Model\Repository\DefaultRepository;
use PDO;

/**
 * Class PetRepository
 * @package App\Modules\Pet\Model\Repository
 */
class PetRepository extends DefaultRepository
{
    /** @var \App\Modules\Pet\Model\Repository\PetImageRepository */
    protected $petImageRepository;

    /**
     * PetRepository constructor.
     * @param PDO $db
     * @param ValidatorInterface $validator
     * @param \App\Modules\Pet\Model\Repository\PetImageRepository $petImageRepository
     */
    public function __construct(
        PDO $db, ValidatorInterface $validator,
        PetImageRepository $petImageRepository
    ) {
        $this->table = "pet";
        $this->entityClass = PetEntity::class;
        $this->petImageRepository = $petImageRepository;
        parent::__construct($db, $validator);
    }

    /**
     * @param \App\Modules\Pet\Model\Entity\PetEntity $entity
     * @return bool
     * @throws \Exception
     */
    public function save($entity): bool
    {
        if ($imageEntity = $entity->getImage()) {
            $this->petImageRepository->save($imageEntity);
        }

        return parent::save($entity);
    }
}
