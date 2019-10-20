<?php

namespace App\Common\Setup;

use Framework\Api\InstallerInterface;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Installer
 * @package App\Common\Setup
 */
class Installer implements InstallerInterface
{
    /** @var PDO $pdo */
    protected $pdo;

    /** @var string $sqlFile */
    protected $sqlFile;

    /** @var OutputInterface */
    protected $output;

    /**
     * InstallDatabase constructor.
     * @param PDO $pdo
     * @param string $sqlFile
     * @param OutputInterface $output
     */
    public function __construct(PDO $pdo, $sqlFile, OutputInterface $output)
    {
        $this->pdo = $pdo;
        $this->sqlFile = $sqlFile;
        $this->output = $output;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $req = file_get_contents($this->sqlFile);
        $req = str_replace("\n", "", $req);
        $req = str_replace("\r", "", $req);

        $this->pdo->exec($req);
        $this->installModules();
    }

    protected function installModules()
    {
        $moduleDirs = Yaml::parseFile(APP_ETC_DIR . '/config.yaml')['modules'];
        foreach ($moduleDirs as $moduleName => $value) {
            if ($value['enabled']) {
                $this->output->writeln("Installing $moduleName Module...");
                $this->installModule($moduleName);
                $this->output->writeln("Module $moduleName installed");
            }
        }
    }

    protected function installModule($moduleName)
    {
        $pattern = MODULES_DIR . "/$moduleName/etc/entities/*.yaml";
        $entityFiles = glob($pattern);
        foreach ($entityFiles as $entityFile) {
            $entity = Yaml::parseFile($entityFile);
            $this->installEntity($entity);
        }
    }

    /**
     * @param array $entity
     */
    protected function installEntity($entity)
    {
        $table = $entity['table'];
        $fields = $entity['fields'];

        $fieldsPart = '';

        foreach ($fields as $field) {
            $fieldsPart .= $field['column'] . " " . $field['type'];
            if (!empty($field['constraints'])) {
                foreach ($field['constraints'] as $key => $value) {
                    if ($key === 'nullable' && $value === false) {
                        $fieldsPart .= ' NOT NULL';
                    }
                }
            }

            $fieldsPart .= ',' . PHP_EOL . '    ';
        }
        $pk = $table . '_PK';

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS $table
(
    id INTEGER NOT NULL,
    $fieldsPart
    CONSTRAINT $pk PRIMARY KEY (id)
);
SQL;

        $this->pdo->exec($sql);
    }
}
