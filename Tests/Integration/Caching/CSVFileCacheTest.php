<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Caching\CSVFileFunctionCache;

class CSVFileCacheTest extends CacheTest
{
    private static $CacheFilePath;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        self::$CacheFilePath = self::$RootCacheDirectory . 'CSVCache.cache';
    }
    
    protected function setUp()
    {
        $this->Cache = new CSVFileFunctionCache(self::$CacheFilePath);
    }
    
    protected function tearDown()
    {
        $this->Cache = null;
        usleep(1000);
        unlink(self::$CacheFilePath);
    }
}
