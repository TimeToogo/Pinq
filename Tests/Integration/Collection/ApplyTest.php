<?php

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'apply']);
        }
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatCollectionApplyOperatesOnTheSameCollection(\Pinq\ICollection $collection, array $data)
    {
        $multiply =
                function (&$i) {
                    $i *= 10;
                };

        $collection->apply($multiply);
        array_walk($data, $multiply);

        $this->assertMatches($collection, $data);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatCollectionApplyWorksOnScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->where(function ($i) { return $i % 2 === 0; })
                ->apply(function (&$i) { $i *= 10; });

        $this->assertMatches($collection, [
            1,
            20,
            3,
            40,
            5,
            60,
            7,
            80,
            9,
            100
        ]);
    }

    public function arraysOfString()
    {
        return $this->getImplementations([
            ['d', 'b', 'q', 'l'],
            ['a', 'c', 'd', 'a'],
            ['d', 'd', 'f', 'q'],
            ['t', 'a', 'v', 'm'],
        ]);
    }

    /**
     * @dataProvider arraysOfString
     */
    public function testThatCollectionApplyWorks(\Pinq\ICollection $collection, array $data)
    {
        $collection->apply(function (&$i) { sort($i); });

        $this->assertMatches($collection, [
            ['b', 'd', 'l', 'q'],
            ['a', 'a', 'c', 'd'],
            ['d', 'd', 'f', 'q'],
            ['a', 'm', 't', 'v'],
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testApplyDoesNotWorkAfterProjection(\Pinq\ICollection $collection, array $data)
    {
        $projectedCollection = $collection->select(function ($i) { return $i; });
        $projectedCollection->apply(function (&$i) { $i *= 10; });

        $this->assertMatches($collection, range(1, 10));
        $this->assertMatches($projectedCollection, range(1, 10));
    }
}
