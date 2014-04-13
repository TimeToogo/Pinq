<?php

namespace Pinq\Tests\Integration\Traversable;

class OrderByTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeffered(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeffered([$Traversable, 'OrderByAscending']);
        $this->AssertThatExecutionIsDeffered([$Traversable, 'OrderByDescending']);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatMultipleExecutionIsDeffered(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeffered(function (callable $Function) use ($Traversable) {
            return $Traversable->OrderByAscending($Function)
                    ->ThenByAscending($Function)
                    ->ThenByDescending($Function);
        });
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatOrderByNegatingNumbersIsEquivalentToArrayReverse(\Pinq\ITraversable $Numbers, array $Data)
    {
        $ReversedNumbers = $Numbers->OrderByAscending(function ($I) { return -$I; });
        
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
        return $this->GetImplementations(['Fred', 'Sam', 'Daniel', 'Frank', 'Andrew', 'Taylor', 'Sandy']);
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderStringsByMultipleCharsOrdersCorrectly(\Pinq\ITraversable $Names, array $Data)
    {
        $OrderedNames = $Names
                ->OrderByAscending(function ($I) { return $I[0]; })
                ->ThenByAscending(function ($I) { return $I[2]; });
        
        $this->AssertMatchesValues($OrderedNames, ['Andrew', 'Daniel', 'Frank', 'Fred', 'Sam', 'Sandy', 'Taylor']);
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderStringsCharsAndLengthCharsOrdersCorrectly(\Pinq\ITraversable $Names, array $Data)
    {        
        $OrderedNames = $Names
                ->OrderByAscending(function ($I) { return $I[0]; })
                ->ThenByAscending('strlen');
        
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
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderByAscendingIsEquivalentToOrderByWithAscendingDirection(\Pinq\ITraversable $Names, array $Data)
    {
        $Function = function ($I) { return $I[0]; };
        $OrderedNames = $Names->OrderByAscending($Function);
        $OtherOrderedNames = $Names->OrderBy($Function, \Pinq\Direction::Ascending);
        
        $this->assertSame($OrderedNames->AsArray(), $OtherOrderedNames->AsArray());
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatOrderByDescendingIsEquivalentToOrderByWithDescendingDirection(\Pinq\ITraversable $Names, array $Data)
    {
        $Function = function ($I) { return $I[0]; };
        $OrderedNames = $Names->OrderByDescending($Function);
        $OtherOrderedNames = $Names->OrderBy($Function, \Pinq\Direction::Descending);
        
        $this->assertSame($OrderedNames->AsArray(), $OtherOrderedNames->AsArray());
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatThenByAscendingIsEquivalentToThenByWithAscendingDirection(\Pinq\ITraversable $Names, array $Data)
    {
        $IrrelaventOrderByFunction = function ($I) { return 1; };
        $ThenFunction = function ($I) { return $I[2]; };
        
        $OrderedNames = $Names->OrderByAscending($IrrelaventOrderByFunction)->ThenByAscending($ThenFunction);
        $OtherOrderedNames = $Names->OrderByAscending($IrrelaventOrderByFunction)->ThenBy($ThenFunction, \Pinq\Direction::Ascending);
        
        $this->assertSame($OrderedNames->AsArray(), $OtherOrderedNames->AsArray());
    }
    
    /**
     * @dataProvider Names
     */
    public function testThatThenByDescendingIsEquivalentToThenByWithDescendingDirection(\Pinq\ITraversable $Names, array $Data)
    {
        $IrrelaventOrderByFunction = function ($I) { return 1; };
        $ThenFunction = function ($I) { return $I[2]; };
        
        $OrderedNames = $Names->OrderByAscending($IrrelaventOrderByFunction)->ThenByDescending($ThenFunction);
        $OtherOrderedNames = $Names->OrderByAscending($IrrelaventOrderByFunction)->ThenBy($ThenFunction, \Pinq\Direction::Descending);
        
        $this->assertSame($OrderedNames->AsArray(), $OtherOrderedNames->AsArray());
    }
}