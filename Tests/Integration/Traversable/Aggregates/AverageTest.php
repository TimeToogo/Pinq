<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AverageTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatAverageOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                empty($data) ? null : array_sum($data) / count($data),
                $traversable->average()
        );
    }
}
