<?php
echo "\n Preparando para inicializar base de datos \n";
$user = getenv("PDSSUTILITIES_DBUSER") ? getenv("PDSSUTILITIES_DBUSER") : 'root';
$pass = getenv("PDSSUTILITIES_DBPASSWORD") ?  getenv("PDSSUTILITIES_DBPASSWORD") : 'dbpassword';
$host = getenv("PDSSUTILITIES_DBHOST") ?  getenv("PDSSUTILITIES_DBHOST") : 'localhost';
$databasename = getenv("PDSSUTILITIES_DBNAME") ?  getenv("PDSSUTILITIES_DBNAME") : 'pdss_utilities';
$pdo = new PDO("mysql:host={$host}", $user, $pass);
echo "\n Limpiando base de datos {$databasename} \n";
$pdo->exec("DROP DATABASE IF EXISTS {$databasename};");
echo "\n Creando base de datos {$databasename};";
$pdo->exec("CREATE DATABASE IF NOT EXISTS {$databasename};");
