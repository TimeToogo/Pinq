<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AggregateTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider assocOneToTen
     */
    public function testThatAggregateOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $aggregator =
                function ($a, $b) {
                    return $a * $b;
                };

        $this->assertSame(
                array_reduce($data, $aggregator, 1),
                $traversable->aggregate($aggregator));
    }

    /**
     * @dataProvider tenRandomStrings
     */
    public function testThatAggregateOperatesCorrectlyWithStrings(\Pinq\ITraversable $traversable, array $data)
    {
        $aggregator =
                function ($a, $b) {
                    return $a . $b;
                };

        $this->assertSame(
                array_reduce($data, $aggregator, ''),
                $traversable->aggregate($aggregator));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatAggregateOnEmptyReturnsNull(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertNull($traversable->aggregate(function () { return true; }));
    }
}
