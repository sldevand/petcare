<?php

namespace App\Modules\Activation\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class NotificationEntity
 * @package App\Modules\Activation\Model\Entity
 */
class NotificationEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var int */
    protected $userId;

    protected careId;

    /** @var bool */
    protected $sent;

    /** @var bool */
    protected $read;

    /**
     * NotificationEntity constructor.
     * @param array $attributes
     * @throws \Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/notification.yaml';
        parent::__construct($attributes);
    }
}
