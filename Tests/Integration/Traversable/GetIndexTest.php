<?php

namespace Pinq\Tests\Integration\Traversable;

class GetIndexTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $Traversable, array $Data)
    {
        foreach ($Data as $Key => $Value) {
            $this->assertEquals($Value, $Traversable[$Key]);
        }
    }
}
