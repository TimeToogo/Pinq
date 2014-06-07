<?php

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Static provider to configure and retrieve the cache implementation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Provider
{
    private function __construct()
    {

    }

    /**
     * @var boolean
     */
    private static $isDevelopmentMode = false;

    /**
     * @var boolean
     */
    private static $hasBeenCleared = false;

    /**
     * @var ICacheAdapter|null
     */
    private static $cacheImplementation;

    /**
     * @return ICacheAdapter
     */
    private static function getImplementation()
    {
        if (self::$cacheImplementation === null) {
            self::$cacheImplementation = new NullCache();
        }

        if (self::$isDevelopmentMode && !self::$hasBeenCleared) {
            self::$cacheImplementation->clear();
            self::$hasBeenCleared = true;
        }

        return self::$cacheImplementation;
    }

    /**
     * If set to true, the cache will be cleared when needed.
     *
     * @param boolean $trueOrFalse
     * @return void
     */
    public static function setDevelopmentMode($trueOrFalse)
    {
        self::$isDevelopmentMode = $trueOrFalse;
    }

    /**
     * Returns the configured cache implementation
     *
     * @return ICacheAdater
     */
    public static function getCacheAdapter()
    {
        return self::getImplementation();
    }

    /**
     * Returns a function cache with the underlying configured implementation
     *
     * @return IFunctionCache
     */
    public static function getCache()
    {
        return new FunctionCache(self::getImplementation());
    }

    /**
     * Uses the supplied file to store the parsed functions
     *
     * @param string $fileName The file to cache the data
     * @return void
     */
    public static function setFileCache($fileName)
    {
        self::$cacheImplementation = new CSVFileCache($fileName);
        self::$hasBeenCleared = false;
    }

    /**
     * Uses the supplied directory to store the parsed functions
     *
     * @param string $directory The directory to cache the data
     * @param string $fileExtension The file extension for every cache file
     * @return void
     */
    public static function setDirectoryCache($directory, $fileExtension = DirectoryCache::DEFAULT_EXTENSION)
    {
        self::$cacheImplementation = new DirectoryCache($directory, $fileExtension);
        self::$hasBeenCleared = false;
    }

    /**
     * Uses the supplied doctrine cache to store the parsed functions
     *
     * @param \Doctrine\Common\Cache\Cache $cache The doctrine cache
     * @return void
     */
    public static function setDoctrineCache(\Doctrine\Common\Cache\Cache $cache)
    {
        self::$cacheImplementation = new DoctrineCacheAdapter($cache);
        self::$hasBeenCleared = false;
    }

    /**
     * Uses the supplied array access cache to store the parsed functions
     *
     * @param \ArrayAccess $cache The array access cache
     * @return void
     */
    public static function setArrayAccessCache(\ArrayAccess $cache)
    {
        self::$cacheImplementation = new ArrayAccessCacheAdapter($cache);
        self::$hasBeenCleared = false;
    }

    /**
     * Uses the supplied cache to store the parsed functions
     *
     * @param IFunctionCache $cache The cache implementations
     * @return void
     */
    public static function setCustomCache(ICacheAdapter $cache)
    {
        self::$cacheImplementation = $cache;
        self::$hasBeenCleared = false;
    }

    /**
     * Removes the configured cache implementation
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$cacheImplementation = null;
        self::$hasBeenCleared = false;
    }
}