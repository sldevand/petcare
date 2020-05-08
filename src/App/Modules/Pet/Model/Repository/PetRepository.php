<?php

namespace App\Modules\Pet\Model\Repository;

use App\Modules\Care\Model\Repository\CareRepository;
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

    /** @var \App\Modules\Image\Service\ImageManager */
    protected $imageManager;

    /** @var \App\Modules\Care\Model\Repository\CareRepository */
    protected $careRepository;

    /**
     * PetRepository constructor.
     * @param \PDO $db
     * @param \Framework\Api\Validator\ValidatorInterface $validator
     * @param \App\Modules\Pet\Model\Repository\PetImageRepository $petImageRepository
     * @param \App\Modules\Care\Model\Repository\CareRepository $careRepository
     * @param \App\Modules\Image\Service\ImageManager $imageManager
     */
    public function __construct(
        PDO $db,
        ValidatorInterface $validator,
        PetImageRepository $petImageRepository,
        CareRepository $careRepository,
        ImageManager $imageManager
    ) {
        $this->table = "pet";
        $this->entityClass = PetEntity::class;
        $this->petImageRepository = $petImageRepository;
        $this->imageManager = $imageManager;
        $this->careRepository = $careRepository;
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

        if (!empty($entity->getImage())) {
            try {
                $imageEntity = $this->petImageRepository->fetchOneBy('petId', $petEntity->getId());
                $imageEntity->setImage($entity->getImage()->getImage());
                $imageEntity->setThumbnail($entity->getImage()->getThumbnail());
            } catch (\Exception $exception) {
                $imageEntity = $entity->getImage();
                $imageEntity->setPetId($petEntity->getId());
            }
            $petImageEntity = $this->petImageRepository->save($imageEntity);
            $petEntity->setImage($petImageEntity);
        }

        return $petEntity;
    }

    /**
     * @param EntityInterface $entity
     * @param bool $thumbnailOnly
     * @return EntityInterface
     */
    public function fetchImage(EntityInterface $entity, bool $thumbnailOnly = false): EntityInterface
    {
        try {
            $image = $this->petImageRepository->fetchOneBy('petId', $entity->getId());

            if (!$thumbnailOnly) {
                $imagePath = $image->getImage();
                if (empty($imagePath)) {
                    return $entity;
                }
                $encodedImage = $this->imageManager->getImageFromPath($imagePath);
                $image->setImage($encodedImage);
            } else {
                $image->setImage('');
            }

            $thumbnailPath = $image->getThumbnail();
            if (empty($thumbnailPath)) {
                return $entity;
            }
            $encodedThumbnail = $this->imageManager->getImageFromPath($thumbnailPath);
            $image->setThumbnail($encodedThumbnail);

            $entity->setImage($image);
        } catch (RepositoryException $e) {
            //Intentionally empty statement
        }

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

    /**
     * @param string $field
     * @param int|string $value
     * @return array
     * @throws Exception
     */
    public function fetchAllByField(string $field, $value): array
    {
        $pets = parent::fetchAllByField($field, $value);

        foreach ($pets as $key => $pet) {
            $pets[$key] = $this->fetchImage($pet, true);
        }

        return $pets;
    }
}
