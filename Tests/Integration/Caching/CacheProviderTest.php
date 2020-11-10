<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching;

class CacheProviderTest extends \Pinq\Tests\PinqTestCase
{
    protected function tearDown(): void
    {
        Caching\CacheProvider::removeCache();
        Caching\CacheProvider::setDevelopmentMode(false);
    }

    public function testThatNoCacheWillReturnANullCache()
    {
        $cacheImplementation = Caching\CacheProvider::getCache();

        $this->assertInstanceOf(
                'Pinq\\Caching\\NullCache',
                $cacheImplementation->getCacheAdapter());
    }

    public function caches()
    {
        return [
            ['setCustomCache', $this->createMock('Pinq\\Caching\\ICacheAdapter'), true],
            ['setArrayAccessCache', new \ArrayObject(), 'Pinq\\Caching\\ArrayAccessCacheAdapter'],
            ['setFileCache', 'php://memory', 'Pinq\\Caching\\CSVFileCache'],
            ['setDirectoryCache', __DIR__, 'Pinq\\Caching\\DirectoryCache']
        ];
    }

    /**
     * @dataProvider caches
     */
    public function testThatProviderWillReturnTheQueryCacheWithTheCorrectInnerCache($method, $cache, $assertSameCache)
    {
        Caching\CacheProvider::$method($cache);

        $functionCache = Caching\CacheProvider::getCache();
        $cacheAdapter = Caching\CacheProvider::getCacheAdapter();

        $this->assertInstanceOf(
                'Pinq\\Caching\\IQueryCache',
                $functionCache);

        $this->assertInstanceOf(
                'Pinq\\Caching\\ICacheAdapter',
                $cacheAdapter);

        $this->assertSame($cacheAdapter, $functionCache->getCacheAdapter());

        if ($assertSameCache === true) {
            $this->assertSame($cache, $cacheAdapter);
        } elseif (is_string($assertSameCache)) {
            $this->assertInstanceOf(
                    $assertSameCache,
                    $cacheAdapter);
        }
    }

    public function testThatDevelopmentModeWillClearTheCacheOnce()
    {
        $functionCacheMock = $this->createMock('Pinq\\Caching\\ICacheAdapter');

        $functionCacheMock
                ->expects($this->once())
                ->method('clear');

        Caching\CacheProvider::setCustomCache($functionCacheMock);
        Caching\CacheProvider::setDevelopmentMode(true);
        //Should clear
        Caching\CacheProvider::getCache();
        //Should not clear again
        Caching\CacheProvider::getCache();
    }
}
