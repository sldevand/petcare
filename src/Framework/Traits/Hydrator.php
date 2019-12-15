<?php

namespace Framework\Traits;

use Exception;

/**
 * Trait Hydrator
 * @package Framework\Traits
 */
trait Hydrator
{
    /**
     * @param array $properties
     * @throws Exception
     */
    public function hydrate(array $properties)
    {
        foreach ($properties as $property => $value) {
            $setMethod = 'set' . ucfirst($property);
            $this->$setMethod($value);
        }
    }
}
