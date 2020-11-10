<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\ICacheAdapter;

abstract class CacheTest extends \Pinq\Tests\PinqTestCase
{
    const TEST_NAMESPACE = 'namespace-';

    protected static $rootCacheDirectory;

    /**
     * @var ICacheAdapter
     */
    protected $cache;

    /**
     * @var ICacheAdapter
     */
    protected $namespacedCache;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$rootCacheDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'CacheFiles' . DIRECTORY_SEPARATOR;

        if (!is_dir(self::$rootCacheDirectory)) {
            mkdir(self::$rootCacheDirectory, 511, true);
        }
    }

    /**
     * @return ICacheAdapter
     */
    abstract protected function setUpCache();

    protected function setUp(): void
    {
        $this->cache = $this->setUpCache();
        $this->namespacedCache = $this->cache->forNamespace(self::TEST_NAMESPACE);
    }

    /**
     * @return void
     */
    protected function tearDownCache()
    {

    }

    protected function tearDown(): void
    {
        $this->cache = null;
        $this->namespacedCache = null;
        $this->tearDownCache();
    }

    public function testThatCacheSavesAllAndRetrievesValues()
    {
        $values = [
            null,
            true,
            false,
            1,
            4.22,
            'abscaacasc',
            [1,2,3,5, 'dsd' => 'sdsdasdsa'],
            new \stdClass(),
        ];

        foreach ($values as $key => $value) {
            $key = 'value' . $key;

            $this->cache->save($key, $value);
            $this->assertTrue($this->cache->contains($key));
            $retrievedValue = $this->cache->tryGet($key);

            if (is_object($value)) {
                $this->assertEquals($value, $retrievedValue);
            } else {
                $this->assertSame($value, $retrievedValue);
            }
        }
    }

    public function testThatTryingToGetNonExistentValueReturnsNull()
    {
        $this->assertNull($this->cache->tryGet('abcde34343'));
    }

    public function testThatCacheSupportsArbitraryKeys()
    {
        $this->cache->save('val-~!@#$%^&*()_+3412-931|\\9rnf2{].>}{P.p[]:"?/,<>ujsdsd', true);

        $this->assertTrue($this->cache->contains('val-~!@#$%^&*()_+3412-931|\\9rnf2{].>}{P.p[]:"?/,<>ujsdsd'));

        $this->cache->remove('val-~!@#$%^&*()_+3412-931|\\9rnf2{].>}{P.p[]:"?/,<>ujsdsd');

        $this->assertFalse($this->cache->contains('val-~!@#$%^&*()_+3412-931|\\9rnf2{].>}{P.p[]:"?/,<>ujsdsd'));
    }

    public function testThatRemovedValueReturnsNull()
    {
        $this->cache->save('value', true);
        $this->cache->remove('value');

        $this->assertNull($this->cache->tryGet('value'));
    }

    public function testThatContainsReturnsTrueForCacheValueAndFalseForNotContainedKey()
    {
        $this->cache->save('1', true);
        $this->cache->save('2', true);

        $this->assertTrue($this->cache->contains('1'));
        $this->assertTrue($this->cache->contains('2'));
        $this->assertFalse($this->cache->contains('3'));

        $this->cache->remove('1');
        $this->cache->remove('2');

        $this->assertFalse($this->cache->contains('1'));
        $this->assertFalse($this->cache->contains('2'));
    }

    public function testThatClearedCacheRemovesSavedValues()
    {
        $this->cache->save('value1', true);
        $this->cache->save('value2', [1,2,3]);

        $this->cache->clear();

        $this->assertNull($this->cache->tryGet('value1'));
        $this->assertNull($this->cache->tryGet('value2'));
    }

    public function testThatForNamespaceReturnsCacheInCorrectNamespace()
    {
        $namespacedCache = $this->cache->forNamespace('--NAMESPACE--');

        $this->assertTrue($namespacedCache->hasNamespace());
        $this->assertSame('--NAMESPACE--', $namespacedCache->getNamespace());
    }

    public function testThatForAnotherNamespaceReturnsCacheInCorrectNamespace()
    {
        $chlidNamespacedCache = $this->namespacedCache->forNamespace('--NAMESPACE--');

        $this->assertTrue($chlidNamespacedCache->hasNamespace());
        $this->assertSame('--NAMESPACE--', $chlidNamespacedCache->getNamespace());
    }

    public function testThatInGlobalNamespaceReturnsCacheWithoutANamespace()
    {
        $globalNamespaceCache = $this->namespacedCache->inGlobalNamespace();

        $this->assertFalse($globalNamespaceCache->hasNamespace());
        $this->assertSame(null, $globalNamespaceCache->getNamespace());
    }

    public function testThatClearingCacheNamespaceOnlyRemovesEntriesInNamespace()
    {
        $this->cache->save('name-foo', true);
        $this->namespacedCache->save('name-bar', [1,2,3]);
        $this->namespacedCache->save('car-three', [1,2,3]);

        $this->namespacedCache->clear();

        $this->assertTrue($this->cache->contains('name-foo'));
        $this->assertFalse($this->namespacedCache->contains('name-bar'));
        $this->assertFalse($this->namespacedCache->contains('car-three'));

        $this->cache->clear();

        $this->assertFalse($this->cache->contains('name-foo'));
    }

    public function testThatNamespacedCacheDoesNotContainValuesOutsideOfNamespace()
    {
        $this->cache->save('not-in-namespace', true);

        $this->assertTrue($this->cache->contains('not-in-namespace'));
        $this->assertFalse($this->namespacedCache->contains('not-in-namespace'));
    }

    public function testThatCacheDoesNotContainValuesNotInGlobalNamespace()
    {
        $this->namespacedCache->save('some-key', true);

        $this->assertTrue($this->namespacedCache->contains('some-key'));
        $this->assertFalse($this->cache->contains('some-key'));
    }

    public function testThatCacheDoesNotGetValuesOutsideOfNamespace()
    {
        $this->cache->save('not-in-namespace', true);

        $this->assertSame($this->cache->tryGet('not-in-namespace'), true);
        $this->assertSame($this->namespacedCache->tryGet('not-in-namespace'), null);
    }

    public function testThatCacheDoesNotRemoveValuesOutsideOfNamespace()
    {
        $this->cache->save('not-in-namespace', true);
        $this->namespacedCache->remove('not-in-namespace');

        $this->assertTrue($this->cache->contains('not-in-namespace'));
        $this->assertFalse($this->namespacedCache->contains('not-in-namespace'));
    }

    public function testThatCacheDoesNotClearValuesOutsideOfNamespace()
    {
        $this->cache->save('not-in-namespace-1', 1);
        $this->cache->save('not-in-namespace-2', 2);
        $this->namespacedCache->save('in-namespace-1', 1);
        $this->namespacedCache->save('in-namespace-2', 2);

        $this->assertTrue($this->namespacedCache->contains('in-namespace-1'));
        $this->assertTrue($this->namespacedCache->contains('in-namespace-2'));

        $this->namespacedCache->clear();

        $this->assertTrue($this->cache->contains('not-in-namespace-1'));
        $this->assertTrue($this->cache->contains('not-in-namespace-2'));
        $this->assertFalse($this->namespacedCache->contains('in-namespace-1'));
        $this->assertFalse($this->namespacedCache->contains('in-namespace-2'));
    }

    public function testThatCacheDoesNotClearValuesOutsideOfChildNamespace()
    {
        $childNamespaceCache = $this->namespacedCache->forNamespace('CHILD::namespace');
        $childNamespaceCache->save('in-child-namespace-1', 1);
        $childNamespaceCache->save('in-child-namespace-2', 2);

        $this->namespacedCache->save('in-namespace', 2);

        $this->assertTrue($childNamespaceCache->contains('in-child-namespace-1'));
        $this->assertTrue($childNamespaceCache->contains('in-child-namespace-2'));

        $childNamespaceCache->clear();

        $this->assertTrue($this->namespacedCache->contains('in-namespace'));
        $this->assertFalse($childNamespaceCache->contains('in-child-namespace-1'));
        $this->assertFalse($childNamespaceCache->contains('in-child-namespace-2'));
    }

    public function testThatNamespaceWillNotClearInAnotherNamespaces()
    {
        $anotherNamespaceCache = $this->namespacedCache->forNamespace('another::namespace');
        $anotherNamespaceCache->save('in-another-namespace-1', 1);
        $anotherNamespaceCache->save('in-another-namespace-2', 2);

        $this->namespacedCache->save('in-namespace', 2);

        $this->namespacedCache->clear();

        $this->assertFalse($this->namespacedCache->contains('in-namespace'));
        $this->assertTrue($anotherNamespaceCache->contains('in-another-namespace-1'));
        $this->assertTrue($anotherNamespaceCache->contains('in-another-namespace-2'));
    }

    public function testThaGlobalNamespaceCacheWillNotClearOtherNamespaces()
    {
        $childNamespaceCache = $this->namespacedCache->forNamespace('CHILD::namespace');
        $childNamespaceCache->save('in-child-namespace-1', 1);
        $childNamespaceCache->save('in-child-namespace-2', 2);

        $this->namespacedCache->save('in-namespace', 2);

        $this->cache->inGlobalNamespace()->clear();

        $this->assertFalse($this->namespacedCache->contains('in-namespace'));
        $this->assertFalse($childNamespaceCache->contains('in-child-namespace-1'));
        $this->assertFalse($childNamespaceCache->contains('in-child-namespace-2'));
    }
}
