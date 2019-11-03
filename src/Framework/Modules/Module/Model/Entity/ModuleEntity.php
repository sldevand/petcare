<?php

namespace Framework\Modules\Module\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class ModuleEntity
 * @package Framework\Modules\Module\Model\Entity
 */
class ModuleEntity extends DefaultEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $version;

    /**
     * PetEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/module.yaml';
        parent::__construct($attributes);
    }
}
