<?php

namespace Pinq\Tests\Integration\Traversable;

class SerializableTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatTraversableIsSerializable(\Pinq\ITraversable $traversable, array $data)
    {
        $serializedTraversable = serialize($traversable);
        $unserializedTraversable = unserialize($serializedTraversable);

        $this->assertEquals(
                $traversable->asArray(),
                $unserializedTraversable->asArray());
    }

    /**
     * @dataProvider everything
     */
    public function testThatTraversableIsSerializableAfterQueries(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->where(function ($i) { return $i !== false; });
                
        $serializedTraversable = serialize($traversable);
        $unserializedTraversable = unserialize($serializedTraversable);

        $this->assertSame(
                $traversable->asArray(),
                $unserializedTraversable->asArray());
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatSerializedTraversableWillEvaluateElementsWithoutRegardForDeterministicness(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->append(range(1, 100))
                ->where(function ($i) { return $i !== false; })
                ->orderByAscending(function () { return mt_rand(); });

        $this->assertNotSame(
                $traversable->asArray(),
                $traversable->asArray());
        
        $serializedTraversable = unserialize(serialize($traversable));
        
        $this->assertSame(
                $serializedTraversable->asArray(), 
                $serializedTraversable->asArray());
    }
}
