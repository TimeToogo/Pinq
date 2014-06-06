<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\DirectoryCache;

class DirectoryCacheTest extends CacheTest
{
    private static $cacheDirectoryPath;

    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$cacheDirectoryPath = self::$rootCacheDirectory . 'DirectoryCache';
    }

    protected function setUp()
    {
        $this->cache = new DirectoryCache(self::$cacheDirectoryPath);
    }

    protected function tearDown()
    {
        $this->cache = null;
        usleep(1000);
        self::deleteDirectory(self::$cacheDirectoryPath);
    }

    private static function deleteDirectory($directory)
    {
        foreach (glob($directory . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
