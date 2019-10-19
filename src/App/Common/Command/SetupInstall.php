<?php

namespace App\Common\Command;

use App\Common\Setup\InstallDatabase;
use Framework\Resource\PDOFactory;
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
     * @return false|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $settings = require __DIR__ . "/../../../settings.php";

        $prodSettings = $settings['settings']['pdo']['prod'];

        $sqliteConnection = PDOFactory::getSqliteConnexion($prodSettings['db-file']);
        $installDb = new InstallDatabase($sqliteConnection, $prodSettings['install-file']);

        $output->writeln('Installing database');

        return $installDb->execute();
    }
}
