<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MinimumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatMinimumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                empty($data) ? null : min($data),
                $traversable->minimum());
    }
}
