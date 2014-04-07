<?php

namespace Pinq\Tests\Integration\Collection;

class RemoveRangeTest extends CollectionTest
{
    
    /**
     * @dataProvider Everything
     */
    public function testThatRemoveRangeRemovesAllValuesFromCollection(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->RemoveRange($Collection->AsArray());
        
        $this->AssertMatchesValues($Collection, []);
    }
    
    /**
     * @dataProvider OneToTenTwice
     */
    public function testThatRemoveRangeWillRemovesIdenticalValuesFromCollectionAndPreserveKeys(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->RemoveRange([1, "2"]);
        
        foreach($Data as $Key => $Value) {
            if($Value === 1) {
               unset($Data[$Key]);
            }
        }
        
        $this->AssertMatchesValues($Collection, $Data);
    }
    
    /**
     * @dataProvider OneToTen
     * @expectedException \Pinq\PinqException
     */
    public function testThatInvalidValueThrowsExceptionWhenCallingRemoveRange(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->RemoveRange(1);
    }
}
