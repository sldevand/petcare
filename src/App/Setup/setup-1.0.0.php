<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

try {
    $sql =
        "CREATE TABLE IF NOT EXISTS pet_entity (
            id INTEGER NOT NULL,
            name TEXT NOT NULL,
            age INTEGER NOT NULL,
            specy TEXT NOT NULL,
            CONSTRAINT pet_entity_PK PRIMARY KEY (id)
        );";

    $pdo = \App\Model\Resource\PDOFactory::getSqliteConnexion();

    $pdo->exec($sql);
} catch (\PDOException $e) {
    echo $e->getMessage();
}