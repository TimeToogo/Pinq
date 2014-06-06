<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching;

class CacheProviderTest extends \Pinq\Tests\PinqTestCase
{
    protected function tearDown()
    {
        Caching\Provider::removeCache();
        Caching\Provider::setDevelopmentMode(false);
    }

    public function testThatNoCacheWillReturnANullCache()
    {
        $cacheImplementation = Caching\Provider::getCache();

        $this->assertInstanceOf(
                'Pinq\\Caching\\NullCache',
                $cacheImplementation->getCacheAdapter());
    }

    public function caches()
    {
        return [
            ['setCustomCache', $this->getMock('Pinq\\Caching\\ICacheAdapter'), true],
            ['setArrayAccessCache', new \ArrayObject(), 'Pinq\\Caching\\ArrayAccessCacheAdapter'],
            ['setFileCache', 'php://memory', 'Pinq\\Caching\\CSVFileCache'],
            ['setDirectoryCache', __DIR__, 'Pinq\\Caching\\DirectoryCache']
        ];
    }

    /**
     * @dataProvider caches
     */
    public function testThatProviderWillReturnTheFunctionCacheWithTheCorrectInnerCache($method, $cache, $assertSameCache)
    {
        Caching\Provider::$method($cache);
        $cacheImplementation = Caching\Provider::getCache();

        $this->assertInstanceOf(
                'Pinq\\Caching\\IFunctionCache',
                $cacheImplementation);

        if ($assertSameCache === true) {
            $this->assertSame($cache, $cacheImplementation->getCacheAdapter());
        } elseif (is_string($assertSameCache)) {
            $this->assertInstanceOf(
                    $assertSameCache,
                    $cacheImplementation->getCacheAdapter());
        }
    }

    public function testThatDevelopmentModeWillClearTheCacheOnce()
    {
        $functionCacheMock = $this->getMock('Pinq\\Caching\\ICacheAdapter');

        $functionCacheMock
                ->expects($this->once())
                ->method('clear');

        Caching\Provider::setCustomCache($functionCacheMock);
        Caching\Provider::setDevelopmentMode(true);
        //Should clear
        Caching\Provider::getCache();
        //Should not clear again
        Caching\Provider::getCache();
    }
}
