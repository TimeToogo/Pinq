<?php 

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AggregateTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatAggregateOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $aggregator = 
                function ($a, $b) {
                    return $a * $b;
                };
        $this->assertEquals(
                array_reduce($data, $aggregator, 1),
                $traversable->aggregate($aggregator));
    }
    
    /**
     * @dataProvider TenRandomStrings
     */
    public function testThatAggregateOperatesCorrectlyWithStrings(\Pinq\ITraversable $traversable, array $data)
    {
        $aggregator = 
                function ($a, $b) {
                    return $a . $b;
                };
        $this->assertEquals(
                array_reduce($data, $aggregator, ''),
                $traversable->aggregate($aggregator));
    }
    
    /**
     * @dataProvider EmptyData
     */
    public function testThatAggregateOnEmptyReturnsNull(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertNull($traversable->aggregate(function () {
            return true;
        }));
    }
}