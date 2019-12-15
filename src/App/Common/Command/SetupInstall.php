<?php

namespace App\Common\Command;

use Exception;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetupInstall
 * @package App\Command
 */
class SetupInstall extends Command
{
    /** @var string */
    protected static $defaultName = 'setup:install';

    protected function configure()
    {
        $this
            ->setDescription('Setup database modules.')
            ->setHelp('This command allows you to setup install your petcare app');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once __DIR__ . '/../../../bootstrap.php';
        require_once VENDOR_DIR . '/autoload.php';
        session_start();
        $settings = require SRC_DIR . '/settings.php';
        $app = new App($settings);
        require_once SRC_DIR . '/dependencies.php';

        $app->getContainer()->get('installer')->execute();
    }
}
