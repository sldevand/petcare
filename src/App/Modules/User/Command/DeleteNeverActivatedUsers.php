<?php

namespace App\Modules\User\Command;

use App\Common\Command;
use App\Modules\User\Cron\DeleteNeverActivatedUsersExecutor;
use Exception;
use Slim\App;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteNeverActivatedUsers
 * @package App\Modules\User\Command
 */
class DeleteNeverActivatedUsers extends Command
{
    const MINUTES_ELAPSED = 10;

    /** @var \App\Modules\User\Cron\DeleteNeverActivatedUsersExecutor */
    protected $executor;

    /**
     * DeleteNeverActivatedUsers constructor.
     * @param null|string $name
     * @param \Slim\App $app
     */
    public function __construct(?string $name, App $app)
    {
        $this->executor = new DeleteNeverActivatedUsersExecutor(['app' => $app]);
        parent::__construct($name, $app);
    }

    protected function configure()
    {
        $this
            ->setDescription($this->executor->getDescription())
            ->setHelp('This command allows you to purge never activated users');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executor->execute();
    }
}
