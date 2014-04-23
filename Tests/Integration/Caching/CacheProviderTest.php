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
                $cacheImplementation->getInnerCache());
    }
    
    public function caches()
    {
        return [
            ['SetCustomCache', $this->getMock('Pinq\\Caching\\IFunctionCache'), true], 
            ['SetArrayAccessCache', new \ArrayObject(), 'Pinq\\Caching\\ArrayAccessCache'], 
            ['SetFileCache', 'php://memory', 'Pinq\\Caching\\CSVFileFunctionCache'], 
            ['SetDirectoryCache', __DIR__, 'Pinq\\Caching\\DirectoryFunctionCache']
        ];
    }
    
    /**
     * @dataProvider Caches
     */
    public function testThatProviderWillReturnTheSecondLevelCacheWithTheCorrectInnerCache($method, $cache, $assertSameCache)
    {
        Caching\Provider::$method($cache);
        $cacheImplementation = Caching\Provider::getCache();
        
        $this->assertInstanceOf(
                'Pinq\\Caching\\SecondLevelFunctionCache',
                $cacheImplementation);
        
        if ($assertSameCache === true) {
            $this->assertSame($cache, $cacheImplementation->getInnerCache());
        }
        else if (is_string($assertSameCache)) {
            $this->assertInstanceOf(
                    $assertSameCache,
                    $cacheImplementation->getInnerCache());
        }
    }
    
    public function testThatDevelopmentModeWillClearTheCacheOnce()
    {
        $functionCacheMock = $this->getMock('Pinq\\Caching\\IFunctionCache');
        
        $functionCacheMock
                ->expects($this->once())
                ->method('Clear');
        
        Caching\Provider::setCustomCache($functionCacheMock);
        Caching\Provider::setDevelopmentMode(true);
        //Should clear
        Caching\Provider::getCache();
        //Should not clear again
        Caching\Provider::getCache();
    }
}