<?php

namespace Pinq\Tests\Integration\Utilities;

use \Pinq\Iterators\Utilities\Dictionary;

class DictionaryTest extends \Pinq\Tests\PinqTestCase
{
    /**
     * @var Dictionary
     */
    private $Dictionary;
    
    protected function setUp()
    {
        $this->Dictionary = new Dictionary();
    }
    
    
    public function DictionaryKeyValues()
    {
        return [
            ['String'],
            [42],
            [123.321],
            [false],
            [true],
            [null],
            [new \stdClass()],
            [[1,new \stdClass(),3]],
            [fopen('php://memory', 'r+')],
        ];
    }
    
    /**
     * @dataProvider DictionaryKeyValues
     */
    public function testThatDictionarySupportsKeyTypes($Key)
    {
        $this->Dictionary->Set($Key, true);
    }
    
    /**
     * @dataProvider DictionaryKeyValues
     */
    public function testThatDictionaryContainsReturnsTrueForSetKeys($Key)
    {
        $this->Dictionary->Set($Key, true);
        
        $this->assertTrue($this->Dictionary->Contains($Key), 'The dictionary should return true for the set key');
    }
    
    /**
     * @dataProvider DictionaryKeyValues
     */
    public function testThatDictionaryContainsReturnsFalseForRemovedKeys($Key)
    {
        $this->Dictionary->Set($Key, true);
        
        $this->Dictionary->Remove($Key);
        
        $this->assertFalse($this->Dictionary->Contains($Key), 'The dictionary should return false for the removed key');
    }
    
    /**
     * @dataProvider DictionaryKeyValues
     */
    public function testThatDictionaryGetReturnsSetValue($Key)
    {
        $Value = new \stdClass();
        $this->Dictionary->Set($Key, $Value);
        
        $this->assertSame($Value, $this->Dictionary->Get($Key), 'The dictionary should return the same value as set');
    }
    
    public function testAddRangeAddsAllKeyValuePairs()
    {
        $Instance = new \stdClass();
        $Range = [
            'string' => $Instance,
            5 => 'foo',
            'bar' => 5.42
        ];
        $this->Dictionary->AddRange($Range);
        
        $this->assertSame($Instance, $this->Dictionary->Get('string'));
        $this->assertSame('foo', $this->Dictionary->Get(5));
        $this->assertSame(5.42, $this->Dictionary->Get('bar'));
    }
   
    public function testRemoveRangeRemovesAllKeyValuePairs()
    {
        $Instance = new \stdClass();
        $Range = [
            'string' => $Instance,
            5 => 'foo',
            'bar' => 5.42
        ];
        $this->Dictionary->AddRange($Range);
        $this->Dictionary->RemoveRange(array_keys($Range));
        
        $this->assertFalse($this->Dictionary->Contains('string'));
        $this->assertFalse($this->Dictionary->Contains(5));
        $this->assertFalse($this->Dictionary->Contains('bar'));
    }
    
    
    public function testGetReturnsNullForUnsetKey()
    {
        $this->Dictionary->Set('boo', 'bar');
        
        $this->assertFalse($this->Dictionary->Contains(5));
        $this->assertNull($this->Dictionary->Get(5));
    }    
    
    public function testThatDictionarySupportsAndPerformsIterationOfKeys()
    {
        $DictionaryKeys = array_map('reset', $this->DictionaryKeyValues());
        foreach($DictionaryKeys as $Index => $Key) {
            $this->Dictionary->Set($Key, $Index);
        }
        
        foreach ($this->Dictionary as $Key) {
            
            $this->assertTrue(in_array($Key, $DictionaryKeys, true));
            
            $Index = array_search($Key, $DictionaryKeys, true);
            $this->assertSame($Index, $this->Dictionary->Get($Key));
        }
    }
}
