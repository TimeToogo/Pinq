<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\ICacheAdapter;
use Pinq\Caching\INamespacedCacheAdapter;
use Pinq\Caching\ArrayAccessCacheAdapter;
use Pinq\Caching\NamespacedCache;

class NamespacedCacheTest extends CacheTest
{
    const TEST_NAMESPACE = 'namespace-';

    /**
     * @var ICacheAdapter
     */
    protected $innerCache;

    /**
     * @var INamespacedCacheAdapter
     */
    protected $cache;

    protected function setUp()
    {
        $this->innerCache = new ArrayAccessCacheAdapter(new ArrayAccessCacheImplementation());
        $this->cache      = new NamespacedCache($this->innerCache, self::TEST_NAMESPACE);
        $this->assertSame($this->innerCache, $this->cache->getInnerCache());
    }

    public function testThatCacheDoesNotContainValuesOutsideOfNamespace()
    {
        $this->innerCache->save('not-in-namespace', true);

        $this->assertTrue($this->innerCache->contains('not-in-namespace'));
        $this->assertFalse($this->cache->contains('not-in-namespace'));
    }

    public function testThatCacheDoesNotGetValuesOutsideOfNamespace()
    {
        $this->innerCache->save('not-in-namespace', true);

        $this->assertSame($this->innerCache->tryGet('not-in-namespace'), true);
        $this->assertSame($this->cache->tryGet('not-in-namespace'), null);
    }

    public function testThatCacheDoesNotRemoveValuesOutsideOfNamespace()
    {
        $this->innerCache->save('not-in-namespace', true);
        $this->cache->remove('not-in-namespace');

        $this->assertTrue($this->innerCache->contains('not-in-namespace'));
        $this->assertFalse($this->cache->contains('not-in-namespace'));
    }

    public function testThatCacheDoesNotClearValuesOutsideOfNamespace()
    {
        $this->innerCache->save('not-in-namespace-1', 1);
        $this->innerCache->save('not-in-namespace-2', 2);
        $this->cache->save('in-namespace-1', 1);
        $this->cache->save('in-namespace-2', 2);

        $this->assertTrue($this->cache->contains('in-namespace-1'));
        $this->assertTrue($this->cache->contains('in-namespace-2'));

        $this->cache->clear();

        $this->assertTrue($this->innerCache->contains('not-in-namespace-1'));
        $this->assertTrue($this->innerCache->contains('not-in-namespace-2'));
        $this->assertFalse($this->cache->contains('in-namespace-1'));
        $this->assertFalse($this->cache->contains('in-namespace-2'));
    }
}
