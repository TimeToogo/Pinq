<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MinimumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatMinimumOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(empty($Data) ? null : min($Data), $Traversable->Minimum());
    }
}
