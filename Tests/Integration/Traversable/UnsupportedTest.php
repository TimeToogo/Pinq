<?php

namespace Pinq\Tests\Integration\Traversable;

class UnsupportedTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatSetIndexThrowsAndException(\Pinq\ITraversable $Traversable, array $Data)
    {
        if(!($Traversable instanceof \Pinq\ICollection)) {
            $this->setExpectedException('\\Pinq\\PinqException');
            $Traversable[0] = null;
        }
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatUnsetIndexThrowsAndException(\Pinq\ITraversable $Traversable, array $Data)
    {
        if(!($Traversable instanceof \Pinq\ICollection)) {
            $this->setExpectedException('\\Pinq\\PinqException');
            unset($Traversable[0]);
        }
    }
}
