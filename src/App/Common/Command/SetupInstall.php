<?php

namespace App\Common\Command;

use Exception;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../../../bootstrap.php';

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
        require_once VENDOR_DIR . '/autoload.php';
        session_start();
        $settings = require_once SRC_DIR . '/settings.php';
        $container = new \Slim\Container($settings);
        require_once SRC_DIR . '/dependencies.php';
        $app = new App($container);

        $app->getContainer()->get('installer')->execute();
    }
}
