<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AggregateTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatAggregateOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Aggregator = function ($A, $B) { return $A * $B; };
        
        $this->assertEquals(array_reduce($Data, $Aggregator, 1), $Traversable->Aggregate($Aggregator));
    }
    
    /**
     * @dataProvider TenRandomStrings
     */
    public function testThatAggregateOperatesCorrectlyWithStrings(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Aggregator = function ($A, $B) { return $A . $B; };
        
        $this->assertEquals(array_reduce($Data, $Aggregator, ''), $Traversable->Aggregate($Aggregator));
    }
    
    /**
     * @dataProvider EmptyData
     */
    public function testThatAggregateOnEmptyReturnsNull(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertNull($Traversable->Aggregate(function () { return true; }));
    }
}
