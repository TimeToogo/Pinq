<?php

namespace Pinq\Tests\Integration\Scheme;

use Pinq\Iterators\IOrderedMap;
use Pinq\Iterators\IIteratorScheme;

class OrderedMapTest extends \Pinq\Tests\PinqTestCase
{
    public function orderedMaps()
    {
        $orderedMaps = [];

        foreach (\Pinq\Iterators\SchemeProvider::getAvailableSchemes() as $scheme) {
            $orderedMaps[] = [$scheme->createOrderedMap(), $scheme];
        }

        return $orderedMaps;
    }

    private function orderedMapKeyValues()
    {
        $unknownType = fopen('php://memory', 'r+');
        fclose($unknownType);

        return [
            'String',
            42,
            123.321,
            false,
            true,
            null,
            'instance' => new \stdClass(),
            [1, new \stdClass(), 3],
            fopen('php://memory', 'r+'),
            $unknownType,
        ];
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapSupportsKeyTypes(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set($key, true);
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapContainsReturnsTrueForSetKeys(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set($key, true);

            $this->assertTrue(
                    $orderedMap->contains($key),
                    'The ordered map should return true for the set key');
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapContainsReturnsFalseForRemovedKeys(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set($key, true);
            $orderedMap->remove($key);

            $this->assertFalse(
                    $orderedMap->contains($key),
                    'The ordered map should return false for the removed key');
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapGetReturnsNullForRemovedKeys(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set($key, true);
            $orderedMap->remove($key);

            $this->assertNull(
                    $orderedMap->get($key),
                    'The ordered map should return null for the removed key');
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapContainsReturnsTrueForNullValueKeys(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set($key, null);

            $this->assertTrue(
                    $orderedMap->contains($key),
                    'The ordered map should return true for the null valued keys');
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatClearingTheSetRemovesAllValues(IOrderedMap $orderedMap)
    {
        $orderedMap->set(true, true);

        $this->assertCount(1, $orderedMap);

        $orderedMap->clear();

        $this->assertCount(0, $orderedMap);
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapGetReturnsSetValue(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $value = new \stdClass();
            $orderedMap->set($key, $value);

            $this->assertSame(
                    $value,
                    $orderedMap->get($key),
                    'The ordered map should return the same value as set');
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testGetReturnsNullForUnsetKey(IOrderedMap $orderedMap)
    {
        foreach ($this->orderedMapKeyValues() as $key) {
            $orderedMap->set('boo', 'bar');

            $this->assertFalse($orderedMap->contains(5));
            $this->assertNull($orderedMap->get(5));
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapSupportsAndPerformsIterationOfKeys(IOrderedMap $orderedMap)
    {
        $orderedmapKeys = $this->orderedMapKeyValues();

        foreach ($orderedmapKeys as $index => $key) {
            $orderedMap->set($key, $index);
        }

        foreach ($orderedMap->keys() as $key) {
            $this->assertTrue(in_array($key, $orderedmapKeys, true));
            $index = array_search($key, $orderedmapKeys, true);
            $this->assertSame($index, $orderedMap->get($key));
        }
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapReturnsAllValuesOfKeyTypes(IOrderedMap $orderedMap)
    {
        $orderedmapKeys = $this->orderedMapKeyValues();

        $values = [];
        foreach ($orderedmapKeys as $index => $key) {
            $orderedMap->set($key, $index);
            $values[] = $index;
        }

        $orderedmapValues = $orderedMap->values();
        sort($values);
        sort($orderedmapValues);
        $this->assertSame($values, $orderedmapValues);
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapUsesArrayIdentity(IOrderedMap $orderedMap)
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
            $orderedMap->set($key, $name);
        }

        foreach ($keys as $name => $key) {
            $this->assertSame($name, $orderedMap->get($key));
        }

        $array = $this->orderedMapKeyValues();
        $identicalArray = $array;

        $originalInstance = $array['instance'];

        $nonIdenticalArray = $array;
        $nonIdenticalArray['instance'] = new \stdClass();

        $anotherIdenticalArray = $nonIdenticalArray;
        $anotherIdenticalArray['instance'] = $originalInstance;

        $instance = new \stdClass();

        $orderedMap->set($array, $instance);

        $this->assertTrue($orderedMap->contains($identicalArray));
        $this->assertSame($instance, $orderedMap->get($identicalArray));

        $this->assertFalse($orderedMap->contains($nonIdenticalArray));
        $this->assertNull($orderedMap->get($nonIdenticalArray));

        $this->assertTrue($orderedMap->contains($anotherIdenticalArray));
        $this->assertSame($instance, $orderedMap->get($anotherIdenticalArray));
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatMapReturnsANewDictionaryWithSameKeysButMappedValues(IOrderedMap $orderedMap, IIteratorScheme $scheme)
    {
        $orderedMap = $scheme->createOrderedMap($scheme->arrayIterator(range(1, 10)));

        $mappedOrderedMap = $orderedMap->map(function () { return null; });

        $this->assertSame(get_class($orderedMap), get_class($mappedOrderedMap));
        $this->assertNotSame($orderedMap, $mappedOrderedMap);

        $this->assertSame($orderedMap->keys(), $mappedOrderedMap->keys());
        $this->assertSame(array_fill(0, 10, null), $mappedOrderedMap->values());
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOrderedMapUsesTheLastValueForAnAssociatedKey(IOrderedMap $orderedMap, IIteratorScheme $scheme)
    {
        $iterator = $scheme->projectionIterator(
                $scheme->arrayIterator(range(0, 10)),
                function () { return null; },
                null);

        $orderedMap = $scheme->createOrderedMap($iterator);
        $this->assertCount(1, $orderedMap);
        $this->assertSame(10, $orderedMap[null]);
    }

    /**
     * @dataProvider orderedMaps
     */
    public function testThatOffsetSetWithNoKeyAppendsWithNextLargestIntGreaterThanOrEqualToZero(IOrderedMap $orderedMap, IIteratorScheme $scheme)
    {
        $orderedMap = $scheme->createOrderedMap($scheme->arrayIterator([-5 => 'foo']));
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
