<?php

namespace App\Modules\Care\Model\Entity;

use Exception;
use Framework\Model\Entity\DefaultEntity;
use Framework\Traits\Entity\EntityTimestampableTrait;

/**
 * Class CareEntity
 * @package App\Modules\Care\Model\Entity
 */
class CareEntity extends DefaultEntity
{
    use EntityTimestampableTrait;

    /** @var string */
    protected $title;

    /** @var int */
    protected $petId;

    /** @var string */
    protected $content;

    /** @var string */
    protected $appointmentDate;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    /**
     * CareEntity constructor.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        $this->configFile = __DIR__ . '/../../etc/entities/care.yaml';
        parent::__construct($attributes);
    }
}
