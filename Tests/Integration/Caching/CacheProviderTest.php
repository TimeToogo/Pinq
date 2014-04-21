<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Caching;

class CacheProviderTest extends \Pinq\Tests\PinqTestCase
{
    protected function tearDown()
    {
        Caching\Provider::RemoveCache();
        Caching\Provider::SetDevelopmentMode(false);
    }
    
    public function testThatNoCacheWillReturnANullCache()
    {
        $CacheImplementation = Caching\Provider::GetCache();
        
        $this->assertInstanceOf('Pinq\\Caching\\NullCache', $CacheImplementation->GetInnerCache());
    }
    
    public function Caches()
    {
        return [
            ['SetCustomCache', $this->getMock('Pinq\\Caching\\IFunctionCache'), true],
            ['SetArrayAccessCache', new \ArrayObject(), 'Pinq\\Caching\\ArrayAccessCache'],
            ['SetFileCache', 'php://memory', 'Pinq\\Caching\\CSVFileFunctionCache'],
            ['SetDirectoryCache', __DIR__, 'Pinq\\Caching\\DirectoryFunctionCache'],
        ];
    }
    
    /**
     * @dataProvider Caches
     */
    public function testThatProviderWillReturnTheSecondLevelCacheWithTheCorrectInnerCache($Method, $Cache, $AssertSameCache)
    {
        Caching\Provider::$Method($Cache);
        
        $CacheImplementation = Caching\Provider::GetCache();
        
        $this->assertInstanceOf('Pinq\\Caching\\SecondLevelFunctionCache', $CacheImplementation);
        if($AssertSameCache === true) {
            $this->assertSame($Cache, $CacheImplementation->GetInnerCache());
        }
        else if(is_string($AssertSameCache)) {
            $this->assertInstanceOf($AssertSameCache, $CacheImplementation->GetInnerCache());
        }
    }
    
    public function testThatDevelopmentModeWillClearTheCacheOnce()
    {
        $FunctionCacheMock = $this->getMock('Pinq\\Caching\\IFunctionCache');
        
        $FunctionCacheMock
                ->expects($this->once())
                ->method('Clear');
        
        Caching\Provider::SetCustomCache($FunctionCacheMock);
        Caching\Provider::SetDevelopmentMode(true);
        
        //Should clear
        Caching\Provider::GetCache();
        
        //Should not clear again
        Caching\Provider::GetCache();
    }
}
