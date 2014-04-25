<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ExistsTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatCountReturnsWhetherItHasAnyElements(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals($traversable->exists(), !empty($data));
    }
}
