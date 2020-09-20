<?php

namespace Framework\Cron;

use Sldevand\Cron\ExecutorInterface;

/**
 * Class AbstractExecutor
 * @package Framework\Cron
 */
abstract class AbstractExecutor implements ExecutorInterface
{
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
}
