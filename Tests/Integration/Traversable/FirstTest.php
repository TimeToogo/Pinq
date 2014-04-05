<?php

namespace Pinq\Tests\Integration\Traversable;

class FirstTest extends TraversableTest
{
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatFirstReturnsTheFirstValue(\Pinq\ITraversable $Numbers, array $Data)
    {
        $this->assertEquals(reset($Data), $Numbers->First());
    }
    
    /**
     * @dataProvider EmptyData
     */
    public function testThatFirstReturnsNullWhenThereAreNoValues(\Pinq\ITraversable $Empty)
    {
        $this->assertEquals(null, $Empty->First());
    }
}
