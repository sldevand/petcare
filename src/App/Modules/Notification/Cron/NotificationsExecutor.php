<?php

namespace App\Modules\Notification\Cron;

use Framework\Cron\AbstractExecutor;

/**
 * Class NotificationsExecutor
 * @package App\Modules\Notifcation\Cron
 */
class NotificationsExecutor extends AbstractExecutor
{
    public function execute()
    {
        $this->output->writeln('TEST');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Notifies all users when they soon have an appointment';
    }
}
