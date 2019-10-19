<?php

namespace App\Model\Entity;

/**
 * Class PetEntity
 * @package App\Model\Entity
 */
class PetEntity extends AbstractEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $dob;

    /** @var string */
    protected $specy;

    /** @var int */
    protected $imageId;

    /** @var string */
    protected $createdAt;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => $this->dob,
            'specy' => $this->specy,
            'imageId' => $this->imageId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return string */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /** @var string */
    protected $updatedAt;


    /**
     * @return string
     */
    public function getDob(): string
    {
        return $this->dob;
    }

    /**
     * @return string
     */
    public function getSpecy(): string
    {
        return $this->specy;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $name
     * @return PetEntity
     */
    public function setName(string $name): PetEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $dob
     * @return PetEntity
     */
    public function setDob(string $dob): PetEntity
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @param string $specy
     * @return PetEntity
     */
    public function setSpecy(string $specy): PetEntity
    {
        $this->specy = $specy;

        return $this;
    }

    /**
     * @param int $imageId
     * @return PetEntity
     */
    public function setImageId(int $imageId): PetEntity
    {
        $this->imageId = $imageId;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return PetEntity
     */
    public function setCreatedAt(string $createdAt): PetEntity
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return PetEntity
     */
    public function setUpdatedAt(string $updatedAt): PetEntity
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
