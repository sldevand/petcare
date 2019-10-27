<?php

namespace App\Common\Setup;

use Exception;
use Framework\Api\Installer\InstallerInterface;
use Framework\Db\Pdo\Query\Builder;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Installer
 * @package App\Common\Setup
 */
class Installer implements InstallerInterface
{
    /** @var PDO */
    protected $pdo;

    /** @var string */
    protected $sqlFile;

    /** @var OutputInterface */
    protected $output;

    /** @var Builder */
    protected $builder;

    /**
     * Installer constructor.
     * @param PDO $pdo
     * @param string $sqlFile
     * @param OutputInterface $output
     * @param Builder $builder
     */
    public function __construct(
        PDO $pdo,
        string $sqlFile,
        OutputInterface $output,
        Builder $builder
    )
    {
        $this->pdo = $pdo;
        $this->sqlFile = $sqlFile;
        $this->output = $output;
        $this->builder = $builder;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $req = file_get_contents($this->sqlFile);
        $req = str_replace("\n", "", $req);
        $req = str_replace("\r", "", $req);

        $this->pdo->exec($req);
        $this->installModules();
    }

    /**
     * @throws Exception
     */
    protected function installModules()
    {
        $moduleDirs = Yaml::parseFile(APP_ETC_DIR . '/config.yaml')['modules'];
        foreach ($moduleDirs as $moduleName => $value) {
            if (!$value['enabled']) {
                continue;
            }
            $this->output->writeln("Installing $moduleName Module...");
            $this->installModule($moduleName);
            $this->output->writeln("Module $moduleName installed");
        }
    }

    /**
     * @param string $moduleName
     * @throws Exception
     */
    protected function installModule($moduleName)
    {
        $pattern = MODULES_DIR . "/$moduleName/etc/entities/*.yaml";
        $entityFiles = glob($pattern);
        foreach ($entityFiles as $entityFile) {
            $sql = $this->builder->createTable($entityFile);
            $this->pdo->exec($sql);
        }
    }
}
