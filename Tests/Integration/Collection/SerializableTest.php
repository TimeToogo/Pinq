<?php

namespace Pinq\Tests\Integration\Collection;

class SerializableTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializable(\Pinq\ICollection $Collection, array $Data)
    {
        $SerializedCollection = serialize($Collection);
        $UnserializedCollection = unserialize($SerializedCollection);
        
        $this->assertEquals($Collection->AsArray(), $UnserializedCollection->AsArray());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializableAfterQueries(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection = $Collection->Where(function ($I) { return $I !== false; });
        
        $SerializedCollection = serialize($Collection);
        $UnserializedCollection = unserialize($SerializedCollection);
        
        $this->assertEquals($Collection->AsArray(), $UnserializedCollection->AsArray());
    }
}
