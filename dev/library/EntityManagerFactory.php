<?php

declare(strict_types=1);

namespace AppLibrary;

use Exception;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


class EntityManagerFactory
{
    static $instance;
    public static function getInstance(): ?EntityManager
    {
        return static::$instance;
    }
    public static function createInstance(array $options, string $cacheDir = '', bool $isDevMode = false,  bool $writeLog = false): EntityManager
    {

        $paths = $options["entities"];
        $driver = $options["driver"];
        $isDevMode = $isDevMode;
        $useSimpleAnnotationReader = false;
        $cache = null;
        $defaultCacheDir = __DIR__ . "/../data/DoctrineORMModule/";

        if (empty($cacheDir)) {
            $cacheDir = $defaultCacheDir;
        }

        if (!$isDevMode && !file_exists($cacheDir)) {
            throw new Exception("The directory " . $cacheDir . " does not exist");
        }

        $proxyDir = $cacheDir . "/Proxy";
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

        $entityManager = EntityManager::create($driver, $config);
        static::$instance = $entityManager;
        return $entityManager;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }
}
