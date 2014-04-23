<?php 

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class CountTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCountReturnsTheAmountOfElements(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals($traversable->count(), count($data));
    }
}