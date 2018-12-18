<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

try {

    $pdo = \App\Model\Resource\PDOFactory::getSqliteConnexion(__DIR__.'/../../../database/petcare-test.db');
    $req='';
    $req=file_get_contents (__DIR__."/sql/setup-1.0.0.sql");
    $req=str_replace("\n","",$req);
    $req=str_replace("\r","",$req);
    $pdo->exec($req);

} catch (\PDOException $e) {
    echo $e->getMessage();
}