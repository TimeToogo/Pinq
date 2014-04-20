<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Caching\DirectoryFunctionCache;

class DirectoryCacheTest extends CacheTest
{
    private static $CacheDirectoryPath;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        self::$CacheDirectoryPath = self::$RootCacheDirectory . 'DirectoryCache';
    }
    
    protected function setUp()
    {
        $this->Cache = new DirectoryFunctionCache(self::$CacheDirectoryPath);
    }
    
    protected function tearDown()
    {
        $this->Cache = null;
        usleep(1000);
        self::DeleteDirectory(self::$CacheDirectoryPath);
    }
    
    private static function DeleteDirectory($Directory)
    {
        foreach(glob($Directory . DIRECTORY_SEPARATOR . '*') as $Path) {
            if(is_dir($Path)) {
                self::DeleteDirectory($Path); 
            }
            else {
                unlink($Path); 
            }
        }
        rmdir($Directory);
    }
}
