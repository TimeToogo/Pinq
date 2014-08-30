<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\CSVFileCache;

class CSVFileCacheTest extends CacheTest
{
    private static $cacheFilePath;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$cacheFilePath = self::$rootCacheDirectory . 'CSVCache.cache';
    }

    protected function setUpCache()
    {
        return new CSVFileCache(self::$cacheFilePath);
    }

    protected function tearDownCache()
    {
        usleep(1000);
        unlink(self::$cacheFilePath);
    }
}
