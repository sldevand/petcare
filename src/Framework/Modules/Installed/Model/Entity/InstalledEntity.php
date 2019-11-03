<?php

namespace Framework\Modules\Installed\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;

/**
 * Class InstalledEntity
 * @package Framework\Modules\Installed\Model\Entity
 */
class InstalledEntity extends DefaultEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $version;

    /**
     * InstalledEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/installed.yaml';
        parent::__construct($attributes);
    }
}
