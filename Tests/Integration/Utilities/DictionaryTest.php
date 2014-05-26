<?php

namespace Pinq\Tests\Integration\Utilities;

use Pinq\Iterators\Utilities\Dictionary;

class DictionaryTest extends \Pinq\Tests\PinqTestCase
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    protected function setUp()
    {
        $this->dictionary = new Dictionary();
    }

    public function dictionaryKeyValues()
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
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionarySupportsKeyTypes($key)
    {
        $this->dictionary->set($key, true);
    }

    /**
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionaryContainsReturnsTrueForSetKeys($key)
    {
        $this->dictionary->set($key, true);

        $this->assertTrue(
                $this->dictionary->contains($key),
                'The dictionary should return true for the set key');
    }

    /**
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionaryContainsReturnsFalseForRemovedKeys($key)
    {
        $this->dictionary->set($key, true);
        $this->dictionary->remove($key);

        $this->assertFalse(
                $this->dictionary->contains($key),
                'The dictionary should return false for the removed key');
    }

    /**
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionaryGetReturnsNullForRemovedKeys($key)
    {
        $this->dictionary->set($key, true);
        $this->dictionary->remove($key);

        $this->assertNull(
                $this->dictionary->get($key),
                'The dictionary should return null for the removed key');
    }

    /**
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionaryContainsReturnsTrueForNullValueKeys($key)
    {
        $this->dictionary->set($key, null);

        $this->assertTrue(
                $this->dictionary->contains($key),
                'The dictionary should return true for the null valued keys');
    }

    /**
     * @dataProvider dictionaryKeyValues
     */
    public function testThatDictionaryGetReturnsSetValue($key)
    {
        $value = new \stdClass();
        $this->dictionary->set($key, $value);

        $this->assertSame(
                $value,
                $this->dictionary->get($key),
                'The dictionary should return the same value as set');
    }

    public function testAddRangeAddsAllKeyValuePairs()
    {
        $instance = new \stdClass();
        $range = ['string' => $instance, 5 => 'foo', 'bar' => 5.42];
        $this->dictionary->addRange($range);

        $this->assertSame($instance, $this->dictionary->get('string'));
        $this->assertSame('foo', $this->dictionary->get(5));
        $this->assertSame(5.42, $this->dictionary->get('bar'));
    }

    public function testRemoveRangeRemovesAllKeyValuePairs()
    {
        $instance = new \stdClass();
        $range = ['string' => $instance, 5 => 'foo', 'bar' => 5.42];
        $this->dictionary->addRange($range);
        $this->dictionary->removeRange(array_keys($range));

        $this->assertFalse($this->dictionary->contains('string'));
        $this->assertFalse($this->dictionary->contains(5));
        $this->assertFalse($this->dictionary->contains('bar'));
    }

    public function testGetReturnsNullForUnsetKey()
    {
        $this->dictionary->set('boo', 'bar');

        $this->assertFalse($this->dictionary->contains(5));
        $this->assertNull($this->dictionary->get(5));
    }

    public function testThatDictionarySupportsAndPerformsIterationOfKeys()
    {
        $dictionaryKeys = array_map('reset', $this->dictionaryKeyValues());

        foreach ($dictionaryKeys as $index => $key) {
            $this->dictionary->set($key, $index);
        }

        foreach ($this->dictionary as $key) {
            $this->assertTrue(in_array($key, $dictionaryKeys, true));
            $index = array_search($key, $dictionaryKeys, true);
            $this->assertSame($index, $this->dictionary->get($key));
        }
    }

    public function testThatDictionaryReturnsAllValuesOfKeyTypes()
    {
        $dictionaryKeys = array_map('reset', $this->dictionaryKeyValues());
        
        $values = [];
        foreach ($dictionaryKeys as $index => $key) {
            $this->dictionary->set($key, $index);
            $values[] = $index;
        }
        
        $dictionaryValues = $this->dictionary->values();
        sort($values);
        sort($dictionaryValues);
        $this->assertSame($values, $dictionaryValues);
    }

    public function testThatDictionaryUsesArrayIdentity()
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
            $this->dictionary->set($key, $name);
        }

        foreach ($keys as $name => $key) {
            $this->assertSame($name, $this->dictionary->get($key));
        }
        
        
        $array = array_map('reset', $this->dictionaryKeyValues());
        $identicalArray = $array;
        
        $originalInstance = $array['instance'];
        
        $nonIdenticalArray = $array;
        $nonIdenticalArray['instance'] = new \stdClass();
        
        $anotherIdenticalArray = $nonIdenticalArray;
        $anotherIdenticalArray['instance'] = $originalInstance;
        
        $instance = new \stdClass();
        
        $this->dictionary->set($array, $instance);
        
        $this->assertTrue($this->dictionary->contains($identicalArray));
        $this->assertSame($instance, $this->dictionary->get($identicalArray));
        
        $this->assertFalse($this->dictionary->contains($nonIdenticalArray));
        $this->assertNull($this->dictionary->get($nonIdenticalArray));
        
        $this->assertTrue($this->dictionary->contains($anotherIdenticalArray));
        $this->assertSame($instance, $this->dictionary->get($anotherIdenticalArray));
    }
    
}
