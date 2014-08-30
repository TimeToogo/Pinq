<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\DirectoryCache;

class DirectoryCacheTest extends DirectoryCacheTestBase
{
    protected function setUpCache()
    {
        return new DirectoryCache(self::$cacheDirectoryPath);
    }
}
