<?php

namespace Pinq\Tests\Integration\Utilities;

use Pinq\Iterators\Utilities\OrderedMap;

class OrderedMapTest extends \Pinq\Tests\PinqTestCase
{
    /**
     * @var OrderedMap
     */
    private $orderedMap;

    protected function setUp()
    {
        $this->orderedMap = new OrderedMap();
    }

    public function orderedmapKeyValues()
    {
        $unknownType = fopen('php://memory', 'r+');
        fclose($unknownType);
        
        return [
            ['String'],
            [42],
            [123.321],
            [false],
            [true],
            [null],
            'instance' => [new \stdClass()],
            [[1, new \stdClass(), 3]],
            [fopen('php://memory', 'r+')],
            [$unknownType],
        ];
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapSupportsKeyTypes($key)
    {
        $this->orderedMap->set($key, true);
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapContainsReturnsTrueForSetKeys($key)
    {
        $this->orderedMap->set($key, true);

        $this->assertTrue(
                $this->orderedMap->contains($key),
                'The ordered map should return true for the set key');
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapContainsReturnsFalseForRemovedKeys($key)
    {
        $this->orderedMap->set($key, true);
        $this->orderedMap->remove($key);

        $this->assertFalse(
                $this->orderedMap->contains($key),
                'The ordered map should return false for the removed key');
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapGetReturnsNullForRemovedKeys($key)
    {
        $this->orderedMap->set($key, true);
        $this->orderedMap->remove($key);

        $this->assertNull(
                $this->orderedMap->get($key),
                'The ordered map should return null for the removed key');
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapContainsReturnsTrueForNullValueKeys($key)
    {
        $this->orderedMap->set($key, null);

        $this->assertTrue(
                $this->orderedMap->contains($key),
                'The ordered map should return true for the null valued keys');
    }

    /**
     * @dataProvider orderedmapKeyValues
     */
    public function testThatOrderedMapGetReturnsSetValue($key)
    {
        $value = new \stdClass();
        $this->orderedMap->set($key, $value);

        $this->assertSame(
                $value,
                $this->orderedMap->get($key),
                'The ordered map should return the same value as set');
    }

    public function testGetReturnsNullForUnsetKey()
    {
        $this->orderedMap->set('boo', 'bar');

        $this->assertFalse($this->orderedMap->contains(5));
        $this->assertNull($this->orderedMap->get(5));
    }

    public function testThatOrderedMapSupportsAndPerformsIterationOfKeys()
    {
        $orderedmapKeys = array_map('reset', $this->orderedmapKeyValues());

        foreach ($orderedmapKeys as $index => $key) {
            $this->orderedMap->set($key, $index);
        }

        foreach ($this->orderedMap->keys() as $key) {
            $this->assertTrue(in_array($key, $orderedmapKeys, true));
            $index = array_search($key, $orderedmapKeys, true);
            $this->assertSame($index, $this->orderedMap->get($key));
        }
    }

    public function testThatOrderedMapReturnsAllValuesOfKeyTypes()
    {
        $orderedmapKeys = array_map('reset', $this->orderedmapKeyValues());
        
        $values = [];
        foreach ($orderedmapKeys as $index => $key) {
            $this->orderedMap->set($key, $index);
            $values[] = $index;
        }
        
        $orderedmapValues = $this->orderedMap->values();
        sort($values);
        sort($orderedmapValues);
        $this->assertSame($values, $orderedmapValues);
    }

    public function testThatOrderedMapUsesArrayIdentity()
    {
        $keys = [
            'ints' => [1, 2, 3],
            'reversedInts' => [3, 2, 1],
            'intsWithArray' => [1, 2, [3]],
            'intsWithString' => [1, '2', 3],
            'instance1' => [1, 2, new \stdClass()],
            'instance2' => [1, 2, new \stdClass()],
            'resource1' => [fopen('php://memory', 'r+'), 2, 3],
            'resource2' => [fopen('php://memory', 'r+'), 2, 3],
        ];

        foreach ($keys as $name => $key) {
            $this->orderedMap->set($key, $name);
        }

        foreach ($keys as $name => $key) {
            $this->assertSame($name, $this->orderedMap->get($key));
        }
        
        
        $array = array_map('reset', $this->orderedmapKeyValues());
        $identicalArray = $array;
        
        $originalInstance = $array['instance'];
        
        $nonIdenticalArray = $array;
        $nonIdenticalArray['instance'] = new \stdClass();
        
        $anotherIdenticalArray = $nonIdenticalArray;
        $anotherIdenticalArray['instance'] = $originalInstance;
        
        $instance = new \stdClass();
        
        $this->orderedMap->set($array, $instance);
        
        $this->assertTrue($this->orderedMap->contains($identicalArray));
        $this->assertSame($instance, $this->orderedMap->get($identicalArray));
        
        $this->assertFalse($this->orderedMap->contains($nonIdenticalArray));
        $this->assertNull($this->orderedMap->get($nonIdenticalArray));
        
        $this->assertTrue($this->orderedMap->contains($anotherIdenticalArray));
        $this->assertSame($instance, $this->orderedMap->get($anotherIdenticalArray));
    }

    public function testThatMapReturnsANewDictionaryWithSameKeysButMappedValues()
    {
        $orderedMap = new OrderedMap(range(1, 10));
        $mappedOrderedMap = $orderedMap->map(function () { return null; });
        
        $this->assertSame(get_class($orderedMap), get_class($mappedOrderedMap));
        $this->assertNotSame($orderedMap, $mappedOrderedMap);
        
        $this->assertSame($orderedMap->keys(), $mappedOrderedMap->keys());
        $this->assertSame(array_fill(0, 10, null), $mappedOrderedMap->values());
    }

    public function testThatMapToArrayReturnsAnArrayWithMappedValues()
    {
        $orderedMap = new OrderedMap(range(1, 10));
        $mappedValues = $orderedMap->mapToArray(function ($i) { return $i + 1; });
        
        $this->assertSame(array_combine(range(0, 9), range(2, 11)), $mappedValues);
    }
    
    public function testThatOrderedMapUsesTheLastValueForAnAssociatedKey()
    {
        $iterator = new \Pinq\Iterators\ProjectionIterator(
                new \ArrayIterator(range(0, 10)),
                function () { return null; },
                null);
                
        $orderedMap = new OrderedMap($iterator);
        $this->assertCount(1, $orderedMap);
        $this->assertSame(10, $orderedMap[null]);
    }

    public function testThatOffsetSetWithNoKeyAppendsWithNextLargestIntGreaterThanOrEqualToZero()
    {
        $orderedMap = new OrderedMap([-5 => 'foo']);
        $orderedMap[] = 'bar';
        $orderedMap[7] = 'baz';
        $orderedMap[] = 'qux';
        
        $this->assertSame('foo', $orderedMap->get(-5));
        $this->assertSame('bar', $orderedMap->get(0));
        $this->assertSame('baz', $orderedMap->get(7));
        $this->assertSame('qux', $orderedMap->get(8));
        
        $orderedMap->remove(8);
        
        $this->assertFalse($orderedMap->contains(8));
        
        $orderedMap[] = 'qux1';
        
        $this->assertSame('qux1', $orderedMap->get(8));
        
        $orderedMap->remove(8);
        $orderedMap->remove(7);
        
        $orderedMap[] = 'boo';
        
        $this->assertSame('boo', $orderedMap->get(1));
    }
}
