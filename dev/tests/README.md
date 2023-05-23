# Tests

Comando para ejecutar los test.

    vendor/bin/phpunit

Alternativamente se puede usar el comando:

    vendor/bin/phpunit --bootstrap dev/tests/bootstrap.php dev/tests

Se puede cambiar la base de datos por defecto pasando variables de entorno de la siguiente forma

     PHP_APP_DB_NAME=pdss_utilities PHP_APP_DB_ROOT=postgres PHP_APP_DB_PASSWORD=dbpassword PHP_APP_DB_DRIVER=pdo_pgsql PHP_APP_DB_CHARSET=utf8 PHP_APP_DB_HOST=localhost bash -c "vendor/bin/doctrine orm:schema-tool:update --force"

    PHP_APP_DB_NAME=pdss_utilities PHP_APP_DB_ROOT=postgres PHP_APP_DB_PASSWORD=dbpassword PHP_APP_DB_DRIVER=pdo_pgsql PHP_APP_DB_CHARSET=utf8 PHP_APP_DB_HOST=localhost bash -c "vendor/bin/phpunit"