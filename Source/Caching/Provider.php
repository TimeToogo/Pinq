<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * Static provider to configure and retrieve the cache implementation
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Provider
{
    private function __construct() {}
    
    /**
     * @var boolean
     */
    private static $IsDevelopmentMode = false;
    
    /**
     * @var boolean
     */
    private static $HasBeenCleared = false;
    
    /**
     * @var IFunctionCache|null
     */
    private static $CacheImplementation;
    
    /**
     * @return IFunctionCache
     */
    private static function GetImplementation()
    {
        if(self::$CacheImplementation === null) {
            self::$CacheImplementation = new NullCache();
        }
        
        if(self::$IsDevelopmentMode && !self::$HasBeenCleared) {
            self::$CacheImplementation->Clear();
            self::$HasBeenCleared = true;
        }
        
        return self::$CacheImplementation;
    }
    
    /**
     * If set to true, the cache will be cleared when needed.
     * 
     * @param boolean $TrueOrFalse
     * @return void
     */
    public static function SetDevelopmentMode($TrueOrFalse)
    {
        self::$IsDevelopmentMode = $TrueOrFalse;
    }
    
    /**
     * Returns the configured cache implementation
     * 
     * @return IFunctionCache
     */
    public static function GetCache()
    {
        return new SecondLevelFunctionCache(self::GetImplementation());
    }
    
    /**
     * Uses the supplied file to store the parsed functions
     * 
     * @param string $FileName The file to cache the data
     * @return void
     */
    public static function SetFileCache($FileName)
    {
        self::$CacheImplementation = new CSVFileFunctionCache($FileName);
        self::$HasBeenCleared = false;
    }
    
    /**
     * Uses the supplied directory to store the parsed functions
     * 
     * @param string $Directory The directory to cache the data
     * @param string $FileExtension The file extension for every cache file
     * @return void
     */
    public static function SetDirectoryCache($Directory, $FileExtension = DirectoryFunctionCache::DefaultExtension)
    {
        self::$CacheImplementation = new DirectoryFunctionCache($Directory, $FileExtension);
        self::$HasBeenCleared = false;
    }
    
    /**
     * Uses the supplied doctrine cache to store the parsed functions
     * 
     * @param \Doctrine\Common\Cache\Cache $Cache The doctrine cache
     * @return void
     */
    public static function SetDoctrineCache(\Doctrine\Common\Cache\Cache $Cache)
    {
        self::$CacheImplementation = new DoctrineFunctionCache($Cache);
        self::$HasBeenCleared = false;
    }
    
    /**
     * Uses the supplied array access cache to store the parsed functions
     * 
     * @param \ArrayAccess $Cache The array access cache
     * @return void
     */
    public static function SetArrayAccessCache(\ArrayAccess $Cache)
    {
        self::$CacheImplementation = new ArrayAccessCache($Cache);
        self::$HasBeenCleared = false;
    }
    
    /**
     * Uses the supplied cache to store the parsed functions
     * 
     * @param IFunctionCache $Cache The cache implementations
     * @return void
     */
    public static function SetCustomCache(IFunctionCache $Cache)
    {
        self::$CacheImplementation = $Cache;
        self::$HasBeenCleared = false;
    }
    
    /**
     * Removes the configured cache implementation
     * 
     * @return void
     */
    public static function RemoveCache()
    {
        self::$CacheImplementation = null;
        self::$HasBeenCleared = false;
    }
}

/**
 * Used if no cache is configure, it will be wrapped in a
 * second level cache so no need to do anything
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NullCache implements IFunctionCache {
    public function TryGet($FunctionHash) { return null; }
    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree) {}
    public function Clear() { }
    public function Remove($FunctionHash) {}
}