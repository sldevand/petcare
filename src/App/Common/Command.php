<?php

namespace App\Common;

use Slim\App;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Class Command
 * @package App\Common
 */
class Command extends SymfonyCommand
{
    /** @var \Slim\App */
    protected $app;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /**
     * Command constructor.
     * @param null|string $name
     */
    public function __construct(?string $name, App $app)
    {
        parent::__construct($name);
        $this->app = $app;
        $this->output = $this->app->getContainer()->get('consoleOutput');
    }
}
