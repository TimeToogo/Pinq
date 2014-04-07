<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AverageTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatAverageOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(empty($Data) ? null : (array_sum($Data) / count($Data)), $Traversable->Average());
    }
}
