<?php

namespace Pinq\Tests\Integration\Collection;

class RemoveWhereTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $Collection, array $Data)
    {
        if(count($Data) > 0) {
            $this->AssertThatExecutionIsNotDeferred([$Collection, 'RemoveWhere']);
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatRemoveWhereRemovesItemsWhereTheFunctionReturnsTrueAndPreservesKeys(\Pinq\ICollection $Numbers, array $Data)
    {
        $Predicate = function ($I) { return $I % 2 === 0; };
        
        $Numbers->RemoveWhere($Predicate);
        
        foreach($Data as $Key => $Value) {
            if($Predicate($Value)) {
                unset($Data[$Key]);
            }
        }
        
        $this->AssertMatches($Numbers, $Data);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatRemoveWhereTrueRemovesAllItems(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->RemoveWhere(function () { return true; });
        
        $this->AssertMatchesValues($Collection, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatRemoveWhereFalseRemovesNoItems(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->RemoveWhere(function () { return false; });
        
        $this->AssertMatchesValues($Collection, $Data);
    }
}
