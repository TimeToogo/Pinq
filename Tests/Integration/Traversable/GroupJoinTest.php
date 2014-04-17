<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupJoinTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeferred(function (callable $Function) use ($Traversable) {
            return $Traversable->GroupJoin([])->On($Function)->To($Function);
        });
        
        $this->AssertThatExecutionIsDeferred(function (callable $Function) use ($Traversable) {
            return $Traversable->GroupJoin([])->OnEquality($Function, $Function)->To($Function);
        });
    }
    
    /**
     * @dataProvider Everything
     */
    public function testGroupJoinOnTrueProducesTheCorrectResult(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->GroupJoin($Data)
                ->On(function () { return true; })
                ->To(function ($OuterValue, \Pinq\ITraversable $Group) {
                    return [$OuterValue, $Group->AsArray()];
                });
                
        $CorrectResult = [];
        foreach($Data as $OuterValue) {
            $CorrectResult[] = [$OuterValue, $Data];
        }
                
        $this->AssertMatchesValues($Traversable, $CorrectResult);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testGroupJoinOnFalseProducesEmptyLeftJoin(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->GroupJoin($Data)
                ->On(function () { return false; })
                ->To(function ($OuterValue, \Pinq\ITraversable $Group) {
                    return [$OuterValue, $Group->AsArray()];
                });
                
        $EmptyLeftJoin = [];
        foreach ($Data as $Value) {
            $EmptyLeftJoin[] = [$Value, []];
        }
                
        $this->AssertMatchesValues($Traversable, $EmptyLeftJoin);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testGroupJoinProducesCorrectResult(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->GroupJoin([1, 2, 2, 3, '4', '5'])
                ->On(function ($Outer, $Inner) { return $Outer === $Inner; })
                ->To(function ($Outer, \Pinq\ITraversable $Values) {
                    return $Outer . '-' . $Values->Implode('-');
                });
                
        $this->AssertMatchesValues($Traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testGroupJoinOnEqualityProducesCorrectResult(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->GroupJoin([1, 2, 2, 3, '4', '5'])
                ->OnEquality(function ($Outer) { return $Outer; }, function ($Inner) { return $Inner; })
                ->To(function ($Outer, \Pinq\ITraversable $Values) {
                    return $Outer . '-' . $Values->Implode('-');
                });
                
        $this->AssertMatchesValues($Traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }
}