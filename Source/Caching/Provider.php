<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * Static provider to configure and retrieve a cache implementation.
 */
final class Provider
{
    private function __construct() {}
    
    /**
     * @var boolean
     */
    private static $IsDevelopmentMode = false;
    
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
        
        if(self::$IsDevelopmentMode) {
            self::$CacheImplementation->Clear();
        }
        
        return self::$CacheImplementation;
    }
    
    /**
     * If set to true, the cache will be cleared upon initialization.
     * 
     * @param boolean $TrueOrFalse
     * @return void
     */
    public static function SetDevelopmentMode($TrueOrFalse)
    {
        self::$IsDevelopmentMode = $TrueOrFalse;
    }
    
    /**
     * @return IFunctionCache
     */
    public static function GetCache()
    {
        return new SecondLevelFunctionCache(self::GetImplementation());
    }
    
    public static function SetFileCache($FileName)
    {
        self::$CacheImplementation = new CSVFileFunctionCache($FileName);
    }
    
    public static function SetDirectoryCache($Directory, $FileExtension = DirectoryFunctionCache::DefaultExtension)
    {
        self::$CacheImplementation = new DirectoryFunctionCache($Directory, $FileExtension);
    }
    
    public static function SetDoctrineCache(\Doctrine\Common\Cache\Cache $Cache)
    {
        self::$CacheImplementation = new DoctrineFunctionCache($Cache);
    }
    
    public static function SetArrayAccessCache(\ArrayAccess $Cache)
    {
        self::$CacheImplementation = new ArrayAccessCache($Cache);
    }
    
    public static function SetCustomCache(IFunctionCache $Cache)
    {
        self::$CacheImplementation = $Cache;
    }
    
    public static function RemoveCache()
    {
        self::$CacheImplementation = null;
    }
}

class NullCache implements IFunctionCache {
    public function TryGet($FunctionHash) { return null; }
    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree) {}
    public function Clear() { }
    public function Remove($FunctionHash) {}
}