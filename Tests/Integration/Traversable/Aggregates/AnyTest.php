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
     * @dataProvider oneToTen
     */
    public function testThatAnyReturnsTrueWhenSomeElementMatch(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertTrue($traversable->any(function ($i) { return $i > 5; }));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatAnyReturnsFalseWhenNoElementMatch(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->any(function ($i) { return $i > 25; }));
    }

    /**
     * @dataProvider everything
     */
    public function testThatAnyOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                count(array_filter($data)) > 0,
                $traversable->any());
    }
}
