<?php

namespace Pinq\Tests\Integration\Traversable;

class WhereTest extends TraversableTest
{
    public function testThatWhereTrueDoesNotFilterAnyData()
    {
        $Data = ['test' => 1, 3, 5, 7, 'php' => 8, 10];
        $Numbers = new \Pinq\Traversable($Data);
        
        $AllNumbers = $Numbers->Where(function () { return true; });
        
        $this->AssertMatches($AllNumbers, $Data);
    }
    
    public function testThatWhereFalseFiltersAllItems()
    {
        $Data = ['test' => 1, 3, 5, 7, 'php' => 8, 10];
        $Numbers = new \Pinq\Traversable($Data);
        
        $NoNumbers = $Numbers->Where(function () { return false; });
        
        $this->AssertMatches($NoNumbers, []);
    }
        
    public function testThatElementsAreFilteredFromTraversableAndPreserveKeys()
    {
        $Numbers = new \Pinq\Traversable([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7]);
        
        $EvenNumbers = $Numbers->Where(function ($I) { return $I % 2 === 0; });
        
        $this->AssertMatches($EvenNumbers, [2 => 2, 4 => 4, 6 => 6]);
    }
}
