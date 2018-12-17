<?php

namespace App\Model\Resource;

use \PDO;
use PDOException;

class PDOFactory
{
    /**
     * @return PDO
     */
    public static function getSqliteConnexion()
    {
        $db = new PDO('sqlite:/home/sebastien/PhpstormProjects/petcare/database/petcare.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
