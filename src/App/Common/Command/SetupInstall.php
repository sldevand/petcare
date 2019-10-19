<?php

namespace App\Common\Command;

use App\Common\Setup\Installer;
use Framework\Resource\PDOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require __DIR__ . '/../../../bootstrap.php';
        $settings = require SRC_DIR . '/settings.php';

        $prodSettings = $settings['settings']['pdo']['prod'];

        $sqliteConnection = PDOFactory::getSqliteConnexion($prodSettings['db-file']);
        $installer = new Installer(
            $sqliteConnection,
            $prodSettings['install-file'],
            $output
        );
        $output->writeln('Installing database schema and modules');
        $installer->execute();
    }
}
