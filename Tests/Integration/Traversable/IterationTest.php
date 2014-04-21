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
            $this->assertTrue(isset($Data[$Key]));
            $this->assertSame($Value, $Data[$Key]);
        }
    }
}
