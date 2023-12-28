<?php
ini_set("display_error", 1);
error_reporting(E_ALL);
echo "\n Preparando para insertar datos en la  base de datos \n";
$user = getenv("PDSSUTILITIES_DBUSER") ? getenv("PDSSUTILITIES_DBUSER") : 'root';
$pass = getenv("PDSSUTILITIES_DBPASSWORD") ?  getenv("PDSSUTILITIES_DBPASSWORD") : 'dbpassword';
$host = getenv("PDSSUTILITIES_DBHOST") ?  getenv("PDSSUTILITIES_DBHOST") : 'localhost';
$databasename = getenv("PDSSUTILITIES_DBNAME") ?  getenv("PDSSUTILITIES_DBNAME") : 'gqlpdss_surveydb';
$pdo = new PDO("mysql:host={$host};dbname={$databasename}", $user, $pass);

$sql = file_get_contents(__DIR__ . "/gqlpdss_pdssutilities.sql");
if (empty($sql)) {
    echo "\n No hay datos que insertar";
    exit;
}
echo "\n Insertando datos {$databasename};\n";
echo $sql;
try {
    $pdo->query($sql);
    echo "\n Datos insertados\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
