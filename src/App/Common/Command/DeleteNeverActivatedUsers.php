<?php

namespace App\Common\Command;

use App\Common\Command;
use Exception;
use Slim\App;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteNeverActivatedUsers
 * @package App\Common\Command
 */
class DeleteNeverActivatedUsers extends Command
{
    const MINUTES_ELAPSED = 10;

    /** @var string */
    protected static $defaultName = 'users:purge';

    protected function configure()
    {
        $this
            ->setDescription('Purge never activated users')
            ->setHelp('This command allows you to purge never activated users');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \App\Modules\User\Model\Repository\UserRepository $userRepository */
        $userRepository = $this->app->getContainer()->get('userRepository');
        if ($neverActivatedUsers = $userRepository->purgeNeverActivatedUsers(self::MINUTES_ELAPSED)) {
            $neverActivatedUsersStr = implode(' , ', $neverActivatedUsers);
            $this->output->writeln("Never activated users were deleted ids = $neverActivatedUsersStr");
        }
    }
}
