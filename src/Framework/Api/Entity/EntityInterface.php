<?php

namespace Framework\Api\Entity;

use JsonSerializable;

/**
 * Interface EntityInterface
 * @package Framework\Api\Entity
 */
interface EntityInterface extends JsonSerializable
{
    /**
     * @return array
     */
    public function getFields(): array;

    /**
     * @return int | null
     */
    public function getId(): ?int;

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function setId(int $id): EntityInterface;

    /**
     * @param string $property
     * @return string
     */
    public function getPropertyMethod(string $property): string;
}
