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

    throw new Exception("No hay usuarios registrados en la base de datos para poder hacer pruebas");
}
