<?php

use AppEntities\User;
use AppLibrary\EntityManagerFactory;

require_once __DIR__ . "/../../vendor/autoload.php";
$options = require __DIR__ . "/../config/doctrine.local.php";
$cacheDir = __DIR__ . "/../data/DoctrineORMModule";
$entityManager = EntityManagerFactory::createInstance($options, $cacheDir, true, '');

// Verifica que haya información en la base de datos si no hay la genera

$users = $entityManager->getRepository(User::class)->findAll();
if (count($users) == 0) {
    $insterFile = __DIR__ . "/data/db_initial_data.sql";
    $sql = file_get_contents($insterFile);
    $conn = $entityManager->getConnection()->getWrappedConnection();
    $conn->exec($sql);
}
