<?php
return [
    "driver" => [
        'user'     =>   getenv("PDSSUTILITIES_DBUSER", true) ?  getenv("PDSSUTILITIES_DBUSER", true) : 'root',
        'password' =>   getenv("PDSSUTILITIES_DBPASSWORD", true) ? getenv("PDSSUTILITIES_DBPASSWORD", true) : 'dbpassword',
        'dbname'   =>   getenv("PDSSUTILITIES_DBNAME", true) ? getenv("PDSSUTILITIES_DBNAME", true) : 'pdss_utilities',
        'driver'   =>   getenv("PDSSUTILITIES_DRIVER", true) ? getenv("PDSSUTILITIES_DRIVER", true) : 'pdo_mysql',
        'host'   =>    getenv("PDSSUTILITIES_DBHOST", true) ? getenv("PDSSUTILITIES_DBHOST", true) : 'localhost',
        'charset' =>     'utf8mb4',
        'port' => getenv("PDSSUTILITIES_MYSQL_PORT", true) ? getenv("PDSSUTILITIES_MYSQL_PORT", true) : "3306"
    ],
    "entities" => require __DIR__ . "/doctrine.entities.php"
];
