<?php

namespace Pinq\Tests\Integration\Collection;

use Pinq\Collection;
use Pinq\PinqException;

class SerializableTest extends CollectionTest
{
    public function testThatCollectionIsSerializable()
    {
        $collection = new Collection();

        $unserializedCollection = unserialize(serialize($collection));

        $this->assertEquals(
                $collection->asArray(),
                $unserializedCollection->asArray());
    }

    public static function whereGreaterThanThree($value)
    {
        return $value > 3;
    }

    public function testThatCollectionIsSerializableAfterQueries()
    {
        $collection = Collection::from(range(1, 10));
        $collection = $collection
                ->where([__CLASS__, 'whereGreaterThanThree']);

        $serializedCollection = serialize($collection);
        $unserializedCollection = unserialize($serializedCollection);

        $this->assertEquals(
                $collection->asArray(),
                $unserializedCollection->asArray());
    }

    public function testThatCollectionIsNotSerializableAfterQueriesWithClosures()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Serialization of 'Closure' is not allowed");
        $collection = Collection::from(range(1, 10));
        $collection = $collection
                ->where(function ($i) { return $i !== false; });

        serialize($collection);
    }
}
