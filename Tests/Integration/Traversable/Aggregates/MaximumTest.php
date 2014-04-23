<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MaximumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatMaximumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                empty($data) ? null : max($data),
                $traversable->maximum());
    }
}
