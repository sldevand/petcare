<?php

namespace App\Modules\User\Cron;

use Sldevand\Cron\ExecutorInterface;
use Slim\App;

/**
 * Class DeleteNeverActivatedUsersExecutor
 * @package App\Modules\User\Cron
 */
class DeleteNeverActivatedUsersExecutor implements ExecutorInterface
{
    const MINUTES_ELAPSED = 10;

    /** @var \Slim\App */
    protected $app;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /**
     * DeleteNeverActivatedUsersExecutor constructor.
     * @param array|null $args
     */
    public function __construct(?array $args = null)
    {
        $this->app = $args['app'];
        $this->output = $this->app->getContainer()->get('consoleOutput');
    }

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
