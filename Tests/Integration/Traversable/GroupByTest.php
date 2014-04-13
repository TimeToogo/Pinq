<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupByTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeffered(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeffered([$Traversable, 'GroupBy']);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatGroupByElementReturnsITraversables(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Groups = $Traversable->GroupBy(function ($I) { return $I; });
        foreach ($Groups as $Group) {
            $this->assertInstanceOf(\Pinq\ITraversable::ITraversableType, $Group);
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     * @depends testThatGroupByElementReturnsITraversables
     */
    public function testThatGroupByGroupsTheElementsCorrectlyAndPreservesKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $IsEven = function ($I) { return $I % 2 === 0; };
        
        //First number is odd, so the first group should be the odd group
        list($Odd, $Even) = $Traversable->GroupBy($IsEven)->AsArray();
        
        $this->AssertMatches($Odd, array_filter($Data, function ($I) use($IsEven) { return !$IsEven($I); }));
        $this->AssertMatches($Even, array_filter($Data, $IsEven));
    }
    
}
