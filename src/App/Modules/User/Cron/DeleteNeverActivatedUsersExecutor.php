<?php

namespace App\Modules\User\Cron;

use Framework\Cron\AbstractExecutor;

/**
 * Class DeleteNeverActivatedUsersExecutor
 * @package App\Modules\User\Cron
 */
class DeleteNeverActivatedUsersExecutor extends AbstractExecutor
{
    const MINUTES_ELAPSED = 10;

    /**
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \App\Modules\User\Model\Repository\UserRepository $userRepository */
        $userRepository = $this->app->getContainer()->get('userRepository');
        if ($neverActivatedUsers = $userRepository->purgeNeverActivatedUsers(self::MINUTES_ELAPSED)) {
            $neverActivatedUsersStr = implode(' , ', $neverActivatedUsers);
            $this->output->writeln("Never activated users were deleted ids = $neverActivatedUsersStr");
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Purge never activated users';
    }
}
