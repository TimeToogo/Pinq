<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AllTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testThatAllReturnsTrueIfEmpty(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertTrue($Traversable->All());
    }
    
    public function FalseyValues()
    {
        //                                                      V
        return $this->GetImplementations([1,1,1,1,1,1,10,11,1,1,0,1,1,1,1,]) +
                //                                                                          V
                $this->GetImplementations(['ert','rgrg', 'dgf', 'g4g43', 'as', 'vd', 'dw', '', 'saav']) +
                //                                 V
                $this->GetImplementations([true, false, true]);
    }
    
    /**
     * @dataProvider FalseyValues
     */
    public function testThatAllReturnsFalseIfThereIsAFalsyValue(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertFalse($Traversable->All());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAllOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(empty($Data) ?: (count(array_filter($Data)) === count($Data)), $Traversable->All());
    }
}
