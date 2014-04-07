<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AnyTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testThatAnyReturnsFalseIfEmpty(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertFalse($Traversable->Any());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAnyOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(count(array_filter($Data)) > 0, $Traversable->Any());
    }
}
