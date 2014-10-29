<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class CountTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatCountReturnsTheAmountOfElements(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame($traversable->count(), count($data));
    }
}
