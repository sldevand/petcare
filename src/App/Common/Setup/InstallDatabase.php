<?php

namespace App\Common\Setup;

use Framework\Api\InstallDatabaseInterface;
use PDO;

/**
 * Class InstallDatabase
 * @package App\Setup
 */
class InstallDatabase implements InstallDatabaseInterface
{
    /**
     * @var PDO $pdo
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
     * @return false|int
     */
    public function execute()
    {
        $req = file_get_contents($this->sqlFile);
        $req = str_replace("\n", "", $req);
        $req = str_replace("\r", "", $req);

        return $this->pdo->exec($req);
    }
}
