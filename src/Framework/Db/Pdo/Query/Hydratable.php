<?php

namespace Framework\Db\Pdo\Query;

use Exception;
use Framework\Traits\Hydrator;

/**
 * Class Hydratable
 * @package Framework\Db\Pdo\Query
 */
class Hydratable
{
    use Hydrator;

    /**
     * Hydratable constructor.
     * @param array $properties
     * @throws Exception
     */
    public function __construct($properties = [])
    {
        if (!empty($properties)) {
            $this->hydrate($properties);
        }
    }
}
