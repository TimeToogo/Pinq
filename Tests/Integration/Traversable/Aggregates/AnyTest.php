<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AnyTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider emptyData
     */
    public function testThatAnyReturnsFalseIfEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->any());
    }

    /**
     * @dataProvider everything
     */
    public function testThatAnyOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                count(array_filter($data)) > 0,
                $traversable->any());
    }
}
