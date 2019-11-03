<?php

namespace App\Common\Setup;

use Exception;
use Framework\Api\Installer\InstallerInterface;
use Framework\Db\Pdo\Query\Builder;
use Framework\Modules\Module\Model\Entity\ModuleEntity;
use Framework\Modules\Module\Model\Repository\ModuleRepository;
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

    /** @var ModuleRepository */
    protected $moduleRepository;

    /**
     * Installer constructor.
     * @param PDO $pdo
     * @param string $sqlFile
     * @param OutputInterface $output
     * @param Builder $builder
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(
        PDO $pdo,
        string $sqlFile,
        OutputInterface $output,
        Builder $builder,
        ModuleRepository $moduleRepository
    ) {
        $this->pdo = $pdo;
        $this->sqlFile = $sqlFile;
        $this->output = $output;
        $this->builder = $builder;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->installModules(FRAMEWORK_DIR, true);
        $this->installModules(APP_DIR);
    }

    /**
     * @param string $scopeDir
     * @param bool $fromFramework
     * @throws Exception
     */
    protected function installModules(string $scopeDir, bool $fromFramework = false)
    {
        $configFile = $scopeDir . '/etc/config.yaml';

        $installedModuleNames = [];
        if (!$fromFramework) {
            $installedModuleNames = $this->getInstalledModuleNames();
        }

        $moduleDirs = Yaml::parseFile($configFile)['modules'];
        foreach ($moduleDirs as $moduleName => $value) {
            if ($value['enabled'] === false) {
                continue;
            }

            if (!$fromFramework && in_array($moduleName, $installedModuleNames)) {
                $this->output->writeln("$moduleName Module is already installed !");
                continue;
            }

            $moduleConfig = $this->getModuleConfig($scopeDir, $moduleName);


            $this->output->writeln("Installing $moduleName Module...");
            $this->installModule($scopeDir, $moduleName);
            $this->moduleRepository->save(
                new ModuleEntity(
                    [
                        'name' => $moduleConfig['name'],
                        'version' => $moduleConfig['version']
                    ]
                )
            );
            $this->output->writeln("Module $moduleName installed");
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getInstalledModuleNames()
    {
        $installedModuleNames = [];
        foreach ($this->moduleRepository->fetchAll() as $module) {
            $installedModuleNames[] = $module->getName();
        }

        return $installedModuleNames;
    }

    /**
     * @param string $scopeDir
     * @param string $moduleName
     * @return array
     */
    public function getModuleConfig(string $scopeDir, string $moduleName): array
    {
        return Yaml::parseFile($scopeDir . "/Modules/$moduleName/etc/module.yaml")['module'];
    }

    /**
     * @param string $scopeDir
     * @param string $moduleName
     * @throws Exception
     */
    protected function installModule(string $scopeDir, string $moduleName)
    {
        $pattern = $scopeDir . "/Modules/$moduleName/etc/entities/*.yaml";

        $entityFiles = glob($pattern);
        foreach ($entityFiles as $entityFile) {
            $sql = $this->builder->createTable($entityFile);
            $this->pdo->exec($sql);
        }
    }
}
