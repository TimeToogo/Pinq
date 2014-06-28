<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class IsEmptyTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatCountReturnsWhetherItHasAnyElements(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals($traversable->isEmpty(), empty($data));
    }
}
