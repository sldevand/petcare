<?php

namespace App\Modules\Activation\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class ActivationEntity
 * @package App\Modules\Activation\Model\Entity
 */
class ActivationEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $activationCode;

    /** @var bool */
    protected $mailSent;

    /** @var bool */
    protected $activated;

    /**
     * ActivationEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/activation.yaml';
        parent::__construct($attributes);
    }
}
