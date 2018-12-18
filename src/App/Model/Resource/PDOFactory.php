<?php

namespace App\Model\Resource;

use PDO;
use PDOException;

/**
 * Class PDOFactory
 * @package App\Model\Resource
 */
class PDOFactory
{
    /**
     * @param string $file
     * @return PDO
     * @throws PDOException
     */
    public static function getSqliteConnexion($file)
    {
        $db = new PDO('sqlite:' . $file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
