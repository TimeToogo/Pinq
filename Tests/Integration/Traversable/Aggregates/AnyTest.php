<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AnyTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testThatAnyReturnsFalseIfEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->any());
    }

    /**
     * @dataProvider Everything
     */
    public function testThatAnyOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                count(array_filter($data)) > 0,
                $traversable->any());
    }
}
