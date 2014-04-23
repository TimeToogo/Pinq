<?php 

namespace Pinq\Tests\Integration\Traversable;

class LastTest extends TraversableTest
{
    /**
     * @dataProvider OneToTen
     */
    public function testThatFirstReturnsTheFirstValue(\Pinq\ITraversable $numbers, array $data)
    {
        $this->assertEquals(end($data), $numbers->last());
    }
    
    /**
     * @dataProvider EmptyData
     */
    public function testThatFirstReturnsNullWhenThereAreNoValues(\Pinq\ITraversable $empty)
    {
        $this->assertEquals(null, $empty->first());
    }
}