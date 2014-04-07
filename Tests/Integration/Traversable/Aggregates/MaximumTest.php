<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class MaximumTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatMaximumOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(empty($Data) ? null : max($Data), $Traversable->Maximum());
    }
}
