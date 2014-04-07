<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class SumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatSumOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(empty($Data) ? null : array_sum($Data), $Traversable->Sum());
    }
}
