<?php 

namespace Pinq\Tests\Integration\Traversable;

class SerializableTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializable(\Pinq\ITraversable $traversable, array $data)
    {
        $serializedTraversable = serialize($traversable);
        $unserializedTraversable = unserialize($serializedTraversable);
        
        $this->assertEquals(
                $traversable->asArray(),
                $unserializedTraversable->asArray());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatCollectionIsSerializableAfterQueries(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = 
                $traversable->where(function ($i) {
                    return $i !== false;
                });
        $serializedTraversable = serialize($traversable);
        $unserializedTraversable = unserialize($serializedTraversable);
        
        $this->assertEquals(
                $traversable->asArray(),
                $unserializedTraversable->asArray());
    }
}