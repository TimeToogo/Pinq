<?php

namespace Pinq\Tests\Integration\Caching;

abstract class DirectoryCacheTestBase extends CacheTest
{
    protected static $cacheDirectoryPath;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$cacheDirectoryPath = self::$rootCacheDirectory . 'DirectoryCache';
    }

    protected function tearDownCache()
    {
        usleep(1000);
        self::deleteDirectory(self::$cacheDirectoryPath);
    }

    private static function deleteDirectory($directory)
    {
        foreach (glob($directory . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}
