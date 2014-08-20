<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\ICacheAdapter;

abstract class CacheTest extends \Pinq\Tests\PinqTestCase
{
    protected static $rootCacheDirectory;

    /**
     * @var ICacheAdapter
     */
    protected $cache;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$rootCacheDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'CacheFiles' . DIRECTORY_SEPARATOR;

        if (!is_dir(self::$rootCacheDirectory)) {
            mkdir(self::$rootCacheDirectory, 511, true);
        }
    }

    public function testThatCacheSavesAllAndRetreivesValues()
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

    public function testThatTryingToGetNonExistentExpressionTreeReturnsNull()
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

    public function testThatClearingCacheWithNamespaceOnlyRemovesEntriesWithNamespacedPrefix()
    {
        $this->cache->save('name-foo', true);
        $this->cache->save('name-bar', [1,2,3]);
        $this->cache->save('car-three', [1,2,3]);

        $this->cache->clear('name');

        $this->assertFalse($this->cache->contains('name-foo'));
        $this->assertFalse($this->cache->contains('name-bar'));
        $this->assertTrue($this->cache->contains('car-three'));

        $this->cache->clear('car');

        $this->assertFalse($this->cache->contains('car-three'));
    }

    public function testThatForNamespaceReturnsNamespacedCacheWithNamespacePrefix()
    {
        $namespacedCache = $this->cache->forNamespace('foo-bar-');

        $this->assertInstanceOf('\\Pinq\\Caching\\INamespacedCacheAdapter', $namespacedCache);
        $this->assertNotSame(false, strpos($namespacedCache->getNamespace(), 'foo-bar-'));
    }
}
