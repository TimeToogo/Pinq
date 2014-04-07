<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class CountTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCountReturnsTheAmountOfElements(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals($Traversable->Count(), count($Data));
    }
}
