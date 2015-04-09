<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class SumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatSumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                empty($data) ? null : array_sum($data),
                $traversable->sum()
        );
    }
}
