<?php

namespace Pinq\Tests\Integration\Traversable;

class IterationTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($traversable as $key => $value) {
            $this->assertTrue(isset($data[$key]));
            $this->assertSame($value, $data[$key]);
        }
    }
}
