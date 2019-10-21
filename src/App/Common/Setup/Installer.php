<?php

namespace App\Common\Setup;

use Exception;
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

    const SQLITE_VALID_CASCADE_KEYWORDS = ['delete', 'update'];
    const SQLITE_VALID_DATATYPES = ['numeric', 'text', 'real', 'text', 'blob'];

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
     * @throws Exception
     */
    protected function installEntity($entity)
    {
        $table = $entity['table'];
        $fields = $entity['fields'];

        $fieldsPart = '';
        $foreignKeysPart = '';
        foreach ($fields as $property => $field) {
            if ($field['column'] !== 'id' && $property !== 'id') {
                $fieldsPart .= $field['column'] . " " . $field['type'];
            }

            if (!empty($field['constraints'])) {
                foreach ($field['constraints'] as $constraintKey => $constraintValue) {
                    if ($field['column'] !== 'id' && $property !== 'id') {
                        $fieldsPart .= $this->addNullableConstraint($constraintKey, $constraintValue);
                    }
                    $foreignKeysPart .= $this->addForeignKey($field['column'], $constraintKey, $constraintValue);
                }
            }

            if ($field['column'] !== 'id' && $property !== 'id') {
                $fieldsPart .= ',' . PHP_EOL . '    ';
            }
        }
        $pk = $table . '_PK';

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS $table
(
    id INTEGER NOT NULL,
    $fieldsPart
    CONSTRAINT $pk PRIMARY KEY (id)$foreignKeysPart
);
SQL;

        $this->pdo->exec($sql);
    }

    /**
     * @param string $constraintKey
     * @param string $constraintValue
     * @return string
     */
    protected function addNullableConstraint($constraintKey, $constraintValue)
    {
        if ($constraintKey !== 'nullable' || $constraintValue === true) {
            return '';
        }

        return ' NOT NULL';
    }

    /**
     * @param string $column
     * @param string $constraintKey
     * @param string $constraintValue
     * @return string
     * @throws Exception
     */
    protected function addForeignKey($column, $constraintKey, $constraintValue)
    {
        if ($constraintKey !== 'fk' || !is_array($constraintValue)) {
            return '';
        }

        $fkReference = $constraintValue["reference"];
        $fkTable = $constraintValue["table"];
        $cascadePart = $this->addCascadePart($constraintValue);

        return <<<FK
,
    FOREIGN KEY($column) REFERENCES $fkTable($fkReference) $cascadePart
FK;
    }

    /**
     * @param $constraintValue
     * @return string
     * @throws Exception
     */
    protected function addCascadePart($constraintValue)
    {
        if (empty($constraintValue['cascade'])) {
            return '';
        }

        $this->checkCascadeIsArray($constraintValue['cascade']);

        $cascadePart = '';
        foreach ($constraintValue['cascade'] as $cascadeAction) {
            $this->checkCascadeAction($cascadeAction);
            $cascadePart .= "ON " . strtoupper($cascadeAction) . " CASCADE ";
        }

        return $cascadePart;
    }

    /**
     * @param string $cascadeAction
     * @throws Exception
     */
    protected function checkCascadeAction($cascadeAction)
    {
        if (!in_array(strtolower($cascadeAction), self::SQLITE_VALID_CASCADE_KEYWORDS)) {
            $class = get_class($this);
            throw new Exception(
                "$class::addCascadePart -->
                 $cascadeAction is not a valid cascade action word"
            );
        }
    }

    /**
     * @param $constraintValue
     * @throws Exception
     */
    protected function checkCascadeIsArray($constraintValue)
    {
        if (!is_array($constraintValue)) {
            $class = get_class($this);
            throw new Exception(
                "$class::addCascadePart --> cascade is not a YAML array
                (maybe you forgot the '- ' in front of your array element"
            );
        }
    }
}
