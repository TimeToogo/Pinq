<?php

namespace Pinq\Tests\Integration\Traversable;

class WhereTest extends TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatWhereTrueDoesNotFilterAnyData(\Pinq\ITraversable $Numbers, array $Data)
    {
        $AllNumbers = $Numbers->Where(function () { return true; });
        
        $this->AssertMatches($AllNumbers, $Data);
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatWhereFalseFiltersAllItems(\Pinq\ITraversable $Numbers, array $Data)
    {
        $NoNumbers = $Numbers->Where(function () { return false; });
        
        $this->AssertMatches($NoNumbers, []);
    }
        
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatElementsAreFilteredFromTraversableAndPreserveKeys(\Pinq\ITraversable $Numbers, array $Data)
    {
        $Predicate = function ($I) { return $I % 2 === 0; };
        
        $EvenNumbers = $Numbers->Where($Predicate);
        
        foreach($Data as $Key => $Value) {
            if(!($Predicate($Value))) {
                unset($Data[$Key]);
            }
        }
        
        $this->AssertMatches($EvenNumbers, $Data);
    }
}
