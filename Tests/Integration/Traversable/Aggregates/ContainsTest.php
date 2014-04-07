<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ContainsTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatContainsReturnsTrueForAContainedElement(\Pinq\ITraversable $Traversable, array $Data)
    {
        foreach ($Data as $Value) {
            $this->assertTrue($Traversable->Contains($Value));
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatContainsReturnsWhetherItContainsAnIdenticalElement(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertTrue($Traversable->Contains(1));
        $this->assertFalse($Traversable->Contains("1"));
        
        $this->assertTrue($Traversable->Contains(10));
        $this->assertFalse($Traversable->Contains("10"));
        
        $this->assertFalse($Traversable->Contains(-1));
        $this->assertFalse($Traversable->Contains(0));
        $this->assertFalse($Traversable->Contains(11));
    }
}
