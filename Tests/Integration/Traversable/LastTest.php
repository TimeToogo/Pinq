<?php

namespace Pinq\Tests\Integration\Traversable;

class LastTest extends TraversableTest
{
    /**
     * @dataProvider OneToTen
     */
    public function testThatFirstReturnsTheFirstValue(\Pinq\ITraversable $Numbers, array $Data)
    {
        $this->assertEquals(end($Data), $Numbers->Last());
    }
    
    /**
     * @dataProvider EmptyData
     */
    public function testThatFirstReturnsNullWhenThereAreNoValues(\Pinq\ITraversable $Empty)
    {
        $this->assertEquals(null, $Empty->First());
    }
}
