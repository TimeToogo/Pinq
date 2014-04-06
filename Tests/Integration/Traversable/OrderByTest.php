<?php

namespace Pinq\Tests\Integration\Traversable;

class OrderByTest extends TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatOrderByNegatingNumbersIsEquivalentToArrayReverse(\Pinq\ITraversable $Numbers, array $Data)
    {
        $ReversedNumbers = $Numbers->OrderBy(function ($I) { return -$I; });
        
        $this->AssertMatches($ReversedNumbers, array_reverse($Data, true));
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatDescendingNegatingNumbersIsEquivalentToOriginal(\Pinq\ITraversable $Numbers, array $Data)
    {
        $UnalteredNumbers = $Numbers->OrderByDescending(function ($I) { return -$I; });
        
        $this->AssertMatches($UnalteredNumbers, $Data);
    }
    
    public function Names()
    {
        return $this->TraversablesFor(['Fred', 'Sam', 'Daniel', 'Frank', 'Andrew', 'Taylor', 'Sandy']);
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderStringsByMultipleCharsOrdersCorrectly(\Pinq\ITraversable $Names, array $Data)
    {
        $OrderedNames = $Names
                ->OrderBy(function ($I) { return $I[0]; })
                ->ThenBy(function ($I) { return $I[2]; });
        
        $this->AssertMatchesValues($OrderedNames, ['Andrew', 'Daniel', 'Frank', 'Fred', 'Sam', 'Sandy', 'Taylor']);
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderStringsCharsAndLengthCharsOrdersCorrectly(\Pinq\ITraversable $Names, array $Data)
    {        
        $OrderedNames = $Names
                ->OrderBy(function ($I) { return $I[0]; })
                ->ThenBy('strlen');
        
        $this->AssertMatchesValues($OrderedNames, ['Andrew', 'Daniel', 'Fred',  'Frank', 'Sam', 'Sandy', 'Taylor']);
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderStringsCharsAndLengthCharsDescendingOrdersCorrectly(\Pinq\ITraversable $Names, array $Data)
    {        
        $OrderedNames = $Names
                ->OrderByDescending(function ($I) { return $I[0]; })
                ->ThenByDescending('strlen');
        
        $this->AssertMatchesValues($OrderedNames, ['Taylor', 'Sandy', 'Sam',  'Frank', 'Fred', 'Daniel', 'Andrew']);
    }
}
