<?php

namespace App\Modules\PasswordReset\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class PasswordResetEntity
 * @package App\Modules\PasswordReset\Model\Entity
 */
class PasswordResetEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $resetCode;

    /** @var bool */
    protected $mailSent;

    /** @var bool */
    protected $reset;

    /**
     * PasswordResetEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/passwordReset.yaml';
        parent::__construct($attributes);
    }
}
