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
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function setId(int $id): EntityInterface;

    /**
     * @param string $name
     * @param mixed $value
     * @return EntityInterface
     */
    public function __set(string $name, $value): EntityInterface;

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty(string $name): bool;
}
