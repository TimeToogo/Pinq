<?php 

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MinimumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatMinimumOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                empty($data) ? null : min($data),
                $traversable->minimum());
    }
}