<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MaximumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatMaximumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                empty($data) ? null : max($data),
                $traversable->maximum());
    }
}
