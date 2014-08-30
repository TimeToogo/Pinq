<?php

namespace Pinq\Tests\Integration\Caching;

use Doctrine\Common\Cache\FilesystemCache;
use Pinq\Caching\DoctrineCache;

class DoctrineCacheTest extends DirectoryCacheTestBase
{
    protected function setUpCache()
    {
        return new DoctrineCache(new FilesystemCache(self::$cacheDirectoryPath));
    }
}
