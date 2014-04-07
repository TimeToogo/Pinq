<?php

namespace Pinq\Tests\Integration\Traversable;

class IssetIndexTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatIssetOnValidIndexesReturnTrue(\Pinq\ITraversable $Traversable, array $Data)
    {
        foreach ($Data as $Key => $Value) {
            $this->assertTrue(isset($Traversable[$Key]));
        }
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatIssetOnInvalidIndexesReturnFalse(\Pinq\ITraversable $Traversable, array $Data)
    {
        $NotAnIndex = 0;
        while (isset($Data[$NotAnIndex])) {
            $NotAnIndex++;
        }
        
        $this->assertFalse(isset($Traversable[$NotAnIndex]));
    }
}
