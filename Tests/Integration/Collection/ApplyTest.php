<?php

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $Collection, array $Data)
    {
        if(count($Data) > 0) {
            $this->AssertThatExecutionIsNotDeferred([$Collection, 'Apply']);
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatCollectionApplyOperatesOnTheSameCollection(\Pinq\ICollection $Collection, array $Data)
    {
        $Multiply = function (&$I) { $I *= 10; };
        $Collection->Apply($Multiply);
        
        array_walk($Data, $Multiply);
        $this->AssertMatches($Collection, $Data);
    }
}
