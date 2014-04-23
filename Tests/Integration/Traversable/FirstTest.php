<?php

namespace Pinq\Tests\Integration\Traversable;

class FirstTest extends TraversableTest
{
    /**
     * @dataProvider OneToTen
     */
    public function testThatFirstReturnsTheFirstValue(\Pinq\ITraversable $numbers, array $data)
    {
        $this->assertEquals(reset($data), $numbers->first());
    }

    /**
     * @dataProvider EmptyData
     */
    public function testThatFirstReturnsNullWhenThereAreNoValues(\Pinq\ITraversable $empty)
    {
        $this->assertEquals(null, $empty->first());
    }
}
