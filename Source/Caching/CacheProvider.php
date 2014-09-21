<?php

namespace Pinq\Caching;

use Doctrine\Common\Cache\CacheProvider as DoctrineCacheProvider;

/**
 * Static provider to configure and retrieve the cache implementation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class CacheProvider
{
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

    private function __construct()
    {

    }

    /**
     * If set to true, the cache will be cleared when needed.
     *
     * @param boolean $isDevelopmentMode
     *
     * @return void
     */
    public static function setDevelopmentMode($isDevelopmentMode)
    {
        self::$isDevelopmentMode = $isDevelopmentMode;
    }

    /**
     * Returns the configured cache implementation
     *
     * @return ICacheAdapter
     */
    public static function getCacheAdapter()
    {
        return self::getImplementation();
    }

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
     * Returns a query cache with the configured adapter.
     *
     * @return IQueryCache
     */
    public static function getCache()
    {
        return new QueryCache(self::getImplementation());
    }

    /**
     * Uses the supplied file to store the parsed queries.
     *
     * @param string $fileName The file to cache the data
     *
     * @return void
     */
    public static function setFileCache($fileName)
    {
        self::$cacheImplementation = new CSVFileCache($fileName);
        self::$hasBeenCleared      = false;
    }

    /**
     * Uses the supplied directory to store the parsed queries.
     *
     * @param string $directory     The directory to cache the data
     * @param string $fileExtension The file extension for every cache file
     *
     * @return void
     */
    public static function setDirectoryCache($directory, $fileExtension = DirectoryCache::DEFAULT_EXTENSION)
    {
        self::$cacheImplementation = new DirectoryCache($directory, $fileExtension);
        self::$hasBeenCleared      = false;
    }

    /**
     * Uses the supplied doctrine cache to store the parsed queries.
     *
     * @param DoctrineCacheProvider $cache The doctrine cache
     *
     * @return void
     */
    public static function setDoctrineCache(DoctrineCacheProvider $cache)
    {
        self::$cacheImplementation = new DoctrineCache($cache);
        self::$hasBeenCleared      = false;
    }

    /**
     * Uses the supplied array access cache to store the parsed queries.
     *
     * @param \ArrayAccess $cache The array access cache
     *
     * @return void
     */
    public static function setArrayAccessCache(\ArrayAccess $cache)
    {
        self::$cacheImplementation = new ArrayAccessCacheAdapter($cache);
        self::$hasBeenCleared      = false;
    }

    /**
     * Uses the supplied cache to store the parsed queries.
     *
     * @param ICacheAdapter $cache The cache implementations
     *
     * @return void
     */
    public static function setCustomCache(ICacheAdapter $cache)
    {
        self::$cacheImplementation = $cache;
        self::$hasBeenCleared      = false;
    }

    /**
     * Removes the configured cache implementation.
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$cacheImplementation = null;
        self::$hasBeenCleared      = false;
    }
}
