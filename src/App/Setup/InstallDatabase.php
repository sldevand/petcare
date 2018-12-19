<?php

namespace App\Setup;

use Lib\Api\InstallDatabaseInterface;
use PDO;

/**
 * Class InstallDatabase
 * @package App\Setup
 */
class InstallDatabase implements InstallDatabaseInterface
{
    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    /**
     * @var string $sqlFile
     */
    protected $sqlFile;

    /**
     * InstallDatabase constructor.
     * @param PDO $pdo
     * @param string $sqlFile
     */
    public function __construct(PDO $pdo, $sqlFile)
    {
        $this->pdo = $pdo;
        $this->sqlFile = $sqlFile;
    }

    /**
     * @throws \PDOException
     */
    public function execute()
    {
        $req = '';
        $req = file_get_contents($this->sqlFile);
        $req = str_replace("\n", "", $req);
        $req = str_replace("\r", "", $req);

        $this->pdo->exec($req);
    }
}
