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
        return [
            ['String'],
            [42],
            [123.321],
            [false],
            [true],
            [null],
            [new \stdClass()],
            [[1, new \stdClass(), 3]],
            [fopen('php://memory', 'r+')]
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
}
