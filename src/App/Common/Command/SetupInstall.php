<?php

namespace App\Common\Command;

use App\Common\Command;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Class SetupInstall
 * @package App\Command
 */
class SetupInstall extends Command
{
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
        /** @var \App\Common\Setup\Installer $installer */
        $installer = $this->app->getContainer()->get('installer');
        $installer->execute();
    }
}
