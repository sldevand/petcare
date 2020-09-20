<?php

namespace App\Modules\User\Cron;

use Framework\Cron\AbstractExecutor;
use Anddye\Mailer\Mailer;


/**
 * Class NotificationsExecutor
 * @package App\Modules\User\Cron
 */
class NotificationsExecutor extends AbstractExecutor
{
    public function execute()
    {

    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Notifies all users when they soon have an appointment';
    }
}
