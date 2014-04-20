<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Caching;

class CacheProviderTest extends \Pinq\Tests\PinqTestCase
{
    protected function tearDown()
    {
        Caching\Provider::RemoveCache();
    }
    
    public function testThatProviderWillReturnASecondLevelCache()
    {
        $CacheMock = $this->getMock('Pinq\\Caching\\IFunctionCache');
        
        Caching\Provider::SetCustomCache($CacheMock);
        
        $CacheImplementation = Caching\Provider::GetCache();
        
        $this->assertInstanceOf('Pinq\\Caching\\SecondLevelFunctionCache', $CacheImplementation);
        $this->assertSame($CacheMock, $CacheImplementation->GetInnerCache());
    }
}
