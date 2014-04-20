<?php

namespace Pinq\Tests\Integration\Traversable;

class SerializableTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializable(\Pinq\ITraversable $Traversable, array $Data)
    {
        $SerializedTraversable = serialize($Traversable);
        $UnserializedTraversable = unserialize($SerializedTraversable);
        
        $this->assertEquals($Traversable->AsArray(), $UnserializedTraversable->AsArray());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializableAfterQueries(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable->Where(function ($I) { return $I !== false; });
        
        $SerializedTraversable = serialize($Traversable);
        $UnserializedTraversable = unserialize($SerializedTraversable);
        
        $this->assertEquals($Traversable->AsArray(), $UnserializedTraversable->AsArray());
    }
}
