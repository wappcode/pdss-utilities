<?php
return [
    "driver" => [
        'user'     =>   getenv("PHP_APP_DB_ROOT", true) ?  getenv("PHP_APP_DB_ROOT", true) : 'root',
        'password' =>   getenv("PHP_APP_DB_PASSWORD", true) ? getenv("PHP_APP_DB_PASSWORD", true) : 'dbpassword',
        'dbname'   =>   getenv("PHP_APP_DB_NAME", true) ? getenv("PHP_APP_DB_NAME", true) : 'pdss_utilities',
        'driver'   =>   getenv("PHP_APP_DB_DRIVER", true) ? getenv("PHP_APP_DB_DRIVER", true) : 'pdo_mysql',
        'host'   =>    getenv("PHP_APP_DB_HOST", true) ? getenv("PHP_APP_DB_HOST", true) : '127.0.0.1',
        'charset' =>    getenv("PHP_APP_DB_CHARSET", true) ? getenv("PHP_APP_DB_CHARSET", true) : 'utf8mb4'
    ],
    "entities" => require __DIR__ . "/doctrine.entities.php"
];
