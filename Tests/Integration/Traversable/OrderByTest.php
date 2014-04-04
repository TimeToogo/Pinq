<?php

namespace Pinq\Tests\Integration\Traversable;

class OrderByTest extends TraversableTest
{
    public function testThatOrderByNegatingNumbersIsEquivalentToArrayReverse()
    {
        $Data = ['test' => 1, 3, 5, 7, 'php' => 8, 10];
        $Numbers = new \Pinq\Traversable($Data);
        
        $ReversedNumbers = $Numbers->OrderBy(function ($I) { return -$I; });
        
        $this->AssertMatches($ReversedNumbers, array_reverse($Data, true));
    }
    
    public function testThatDescendingNegatingNumbersIsEquivalentToOriginal()
    {
        $Data = ['test' => 1, 3, 5, 7, 'php' => 8, 10];
        $Numbers = new \Pinq\Traversable($Data);
        
        $UnalteredNumbers = $Numbers->OrderByDescending(function ($I) { return -$I; });
        
        $this->AssertMatches($UnalteredNumbers, $Data);
    }
    
    public function testThatOrderStringsByMultipleCharsOrdersCorrectly()
    {
        $Data = ['Fred', 'Sam', 'Daniel', 'Frank', 'Andrew', 'Taylor', 'Sandy'];
        $Name = new \Pinq\Traversable($Data);
        
        $OrderedNames = $Name
                ->OrderBy(function ($I) { return $I[0]; })
                ->ThenBy(function ($I) { return $I[2]; });
        
        $this->AssertMatchesValues($OrderedNames, ['Andrew', 'Daniel', 'Frank', 'Fred', 'Sam', 'Sandy', 'Taylor']);
    }
    
    public function testThatOrderStringsCharsAndLengthCharsOrdersCorrectly()
    {
        $Data = ['Fred', 'Sam', 'Daniel', 'Frank', 'Andrew', 'Taylor', 'Sandy'];
        $Names = new \Pinq\Traversable($Data);
        
        $OrderedNames = $Names
                ->OrderBy(function ($I) { return $I[0]; })
                ->ThenBy('strlen');
        
        $this->AssertMatchesValues($OrderedNames, ['Andrew', 'Daniel', 'Fred',  'Frank', 'Sam', 'Sandy', 'Taylor']);
    }
}
