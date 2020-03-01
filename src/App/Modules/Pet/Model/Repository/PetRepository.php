<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Image\Service\ImageManager;
use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Validator\ValidatorInterface;
use Framework\Exception\RepositoryException;
use Framework\Model\Repository\DefaultRepository;
use Framework\Traits\Repository\RepositoryTimestampableTrait;
use PDO;

/**
 * Class PetRepository
 * @package App\Modules\Pet\Model\Repository
 */
class PetRepository extends DefaultRepository
{
    use RepositoryTimestampableTrait;

    /** @var \App\Modules\Pet\Model\Repository\PetImageRepository */
    protected $petImageRepository;

    /** @var \App\Modules\Pet\Model\Repository\PetCareRepository */
    protected $petCareRepository;

    /** @var \App\Modules\Image\Service\ImageManager */
    protected $imageManager;

    /**
     * PetRepository constructor.
     * @param \PDO $db
     * @param \Framework\Api\Validator\ValidatorInterface $validator
     * @param \App\Modules\Pet\Model\Repository\PetImageRepository $petImageRepository
     * @param \App\Modules\Pet\Model\Repository\PetCareRepository $petCareRepository
     * @param \App\Modules\Image\Service\ImageManager $imageManager
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetImageRepository $petImageRepository,
        PetCareRepository $petCareRepository,
        ImageManager $imageManager
    ) {
        $this->table = "pet";
        $this->entityClass = PetEntity::class;
        $this->petImageRepository = $petImageRepository;
        $this->petCareRepository = $petCareRepository;
        $this->imageManager = $imageManager;
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
     */
    public function fetchImage(EntityInterface $entity): EntityInterface
    {
        try {
            $image = $this->petImageRepository->fetchOneBy('petId', $entity->getId());

            $imagePath = $image->getImage();
            $encodedImage = $this->imageManager->getImageFromPath($imagePath);
            $image->setImage($encodedImage);

            $entity->setImage($image);
        } catch (RepositoryException $e) {
            //Intentionally empty statement
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function fetchCares(EntityInterface $entity): EntityInterface
    {
        $image = $this->petCareRepository->findAll('petId', $entity->getId());
        $entity->setImage($image);

        return $entity;
    }

    /**
     * @return EntityInterface[]
     * @throws Exception
     */
    public function fetchAll(): array
    {
        $pets = parent::fetchAll();

        foreach ($pets as $key => $pet) {
            $pets[$key] = $this->fetchImage($pet);
        }

        return $pets;
    }

    /**
     * @param int $id
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function fetchOne(int $id): EntityInterface
    {
        $pet = parent::fetchOne($id);

        return $this->fetchImage($pet);
    }

    /**
     * @param string $field
     * @param int|string $value
     * @param string $and
     * @return EntityInterface
     * @throws RepositoryException
     */
    public function fetchOneBy(string $field, $value, string $and = ''): EntityInterface
    {
        $pet = parent::fetchOneBy($field, $value, $and);

        return $this->fetchImage($pet);
    }
}
