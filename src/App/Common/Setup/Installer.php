<?php

namespace App\Common\Setup;

use Exception;
use Framework\Api\Installer\InstallerInterface;
use Framework\Db\Pdo\Query\Builder;
use Framework\Modules\Installed\Model\Entity\InstalledEntity;
use Framework\Modules\Installed\Model\Repository\InstalledRepository;
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

    /** @var InstalledRepository */
    protected $installedRepository;

    /**
     * Installer constructor.
     * @param PDO $pdo
     * @param string $sqlFile
     * @param OutputInterface $output
     * @param Builder $builder
     * @param InstalledRepository $installedRepository
     */
    public function __construct(
        PDO $pdo,
        string $sqlFile,
        OutputInterface $output,
        Builder $builder,
        InstalledRepository $installedRepository
    ) {
        $this->pdo = $pdo;
        $this->sqlFile = $sqlFile;
        $this->output = $output;
        $this->builder = $builder;
        $this->installedRepository = $installedRepository;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->writeInfo("---Start Installation---");
        $this->writeInfo("-Framework Modules installation");
        $this->installModules(FRAMEWORK_DIR);
        $this->writeInfo("-App Modules installation");
        $this->installModules(APP_DIR);
        $this->writeInfo("---End of installation---");
    }

    /**
     * @param string $scopeDir
     * @throws Exception
     */
    protected function installModules(string $scopeDir)
    {
        $configFile = $scopeDir . '/etc/config.yaml';

        try {
            $installedModules = $this->installedRepository->fetchAll();
        } catch (Exception $exception) {
            $installedModules = [];
        }

        $moduleDirs = Yaml::parseFile($configFile)['modules'];

        foreach ($moduleDirs as $moduleName => $value) {
            if ($value['enabled'] === false) {
                $this->writeComment("$moduleName Module is disabled!");
                continue;
            }

            if ($installedModule = $this->isModuleAlreadyInstalled($moduleName, $installedModules)) {
                $name = $installedModule->getName();
                $version = $installedModule->getVersion();
                $this->writeComment("$name Module is already installed with version $version!");
                continue;
            }

            $this->installModule($scopeDir, $moduleName);
            $this->saveModuleVersion($scopeDir, $moduleName);
        }
    }

    protected function isModuleAlreadyInstalled($moduleName, $installedModules)
    {
        foreach ($installedModules as $installedModule) {
            if ($moduleName === $installedModule->getName()) {
                return $installedModule;
            }
        }

        return false;
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
        $this->writeInfo("Installing $moduleName Module...");
        $pattern = $scopeDir . "/Modules/$moduleName/etc/entities/*.yaml";
        $entityFiles = glob($pattern);
        foreach ($entityFiles as $entityFile) {
            $sql = $this->builder->createTable($entityFile);
            $this->pdo->exec($sql);
        }
    }

    /**
     * @param $scopeDir
     * @param $moduleName
     * @throws Exception
     */
    protected function saveModuleVersion($scopeDir, $moduleName)
    {
        $moduleConfig = $this->getModuleConfig($scopeDir, $moduleName);
        $version = $moduleConfig['version'];
        $name = $moduleConfig['name'];
        $this->installedRepository->save(
            new InstalledEntity(
                [
                    'name' => $name,
                    'version' => $version
                ]
            )
        );

        $this->writeInfo("Module $name installed with version $version");
    }

    protected function writeInfo($str)
    {
        $this->output->writeln("<info>$str</info>");
    }

    protected function writeComment($str)
    {
        $this->output->writeln("<comment>$str</comment>");
    }
}
