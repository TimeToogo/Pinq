<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupByTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->GroupBy(function () {});
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeferred([$Traversable, 'GroupBy']);
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
     * @dataProvider OneToTen
     * @depends testThatGroupByElementReturnsITraversables
     */
    public function testThatGroupByMultipleTest(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Groups = $Traversable
                ->GroupBy(function ($I) { return $I % 2 === 0; })
                ->AndBy(function ($I) { return $I % 3 === 0; })
                ->AsArray();
        
        $this->assertCount(4, $Groups);
        $this->AssertMatchesValues($Groups[0], [1, 5, 7]);
        $this->AssertMatchesValues($Groups[1], [2, 4, 8, 10]);
        $this->AssertMatchesValues($Groups[2], [3, 9]);
        $this->AssertMatchesValues($Groups[3], [6]);
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
