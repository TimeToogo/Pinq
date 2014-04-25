<?php

namespace Pinq\Tests\Integration\Traversable;

class LastTest extends TraversableTest
{
    /**
     * @dataProvider oneToTen
     */
    public function testThatFirstReturnsTheFirstValue(\Pinq\ITraversable $numbers, array $data)
    {
        $this->assertEquals(end($data), $numbers->last());
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatFirstReturnsNullWhenThereAreNoValues(\Pinq\ITraversable $empty)
    {
        $this->assertEquals(null, $empty->first());
    }
}
