<?php
namespace App\Model\Resource;

use \PDO;
use PDOException;

class PDOFactory
{
    public static function getSqliteConnexion() {


            $db = new PDO(self::$pdoAdress);
            //$db = new PDO('sqlite:C:\wamp64\www\database\releves.db');
            //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        self::$lastUsedConnexion='sqlite';
        return $db;
    }

    public static function setPdoAddress($address){

        self::$pdoAdress=$address;
    }
}
