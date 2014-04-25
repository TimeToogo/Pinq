<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AverageTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatAverageOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                empty($data) ? null : array_sum($data) / count($data),
                $traversable->average());
    }
}
