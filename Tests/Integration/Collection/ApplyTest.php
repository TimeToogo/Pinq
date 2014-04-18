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
        static $Count = 0;
        $Count++;
        var_dump($Count);
        $Multiply = function (&$I) { $I = "\\r" . $I * 10 . PHP_EOL; };
        $Collection->Apply($Multiply);
        
        array_walk($Data, $Multiply);
        $this->AssertMatches($Collection, $Data);
    }
}
