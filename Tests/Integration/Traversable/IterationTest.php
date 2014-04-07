<?php

namespace Pinq\Tests\Integration\Traversable;

class IterationTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $Traversable, array $Data)
    {
        foreach ($Traversable as $Key => $Value) {
            $this->assertEquals($Value, $Data[$Key]);
        }
    }
}
