<?php 

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class SumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatSumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                empty($data) ? null : array_sum($data),
                $traversable->sum());
    }
}