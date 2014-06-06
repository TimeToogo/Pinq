<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Expressions as O;
use Pinq\Caching\ICacheAdapter;

abstract class CacheTest extends \Pinq\Tests\PinqTestCase
{
    protected static $rootCacheDirectory;

    /**
     * @var ICacheAdapter
     */
    protected $cache;

    public function __construct($name = NULL, array $data = [], $dataName = '')
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
        
        foreach($values as $key => $value) {
            $key = 'value' . $key;
            
            $this->cache->save($key, $value);
            $retrievedValue = $this->cache->tryGet($key);

            if(is_object($value)) {
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
}
