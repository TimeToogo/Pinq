<?php

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    
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
