<?php

namespace Pinq\Tests\Integration\Traversable;

class JoinTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeferred(function (callable $Function) use ($Traversable) {
            return $Traversable->Join([])->On($Function)->To($Function);
        });
        
        $this->AssertThatExecutionIsDeferred(function (callable $Function) use ($Traversable) {
            return $Traversable->Join([])->OnEquality($Function, $Function)->To($Function);
        });
    }
    
    /**
     * @dataProvider Everything
     */
    public function testJoinOnTrueProducesACartesianProduct(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->Join($Data)
                ->On(function () { return true; })
                ->To(function ($OuterValue, $InnerValue) {
                    return [$OuterValue, $InnerValue];
                });
                
        $CartesianProduct = [];
        foreach($Data as $OuterValue) {
            foreach($Data as $InnerValue) {
                $CartesianProduct[] = [$OuterValue, $InnerValue];
            }
        }
                
        $this->AssertMatchesValues($Traversable, $CartesianProduct);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testJoinOnFalseProducesEmpty(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->Join($Data)
                ->On(function () { return false; })
                ->To(function ($OuterValue, $InnerValue) {
                    return [$OuterValue, $InnerValue];
                });
                
        $this->AssertMatches($Traversable, []);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testJoinOnProducesCorrectResult(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->Join([1, 2, 3, '4', '5'])
                ->On(function ($Outer, $Inner) { return $Outer === $Inner; })
                ->To(function ($Outer, $Inner) {
                    return $Outer . '-' . $Inner;
                });
                
        $this->AssertMatchesValues($Traversable, ['1-1', '2-2', '3-3']);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testJoinOnEqualityProducesCorrectResult(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->Join([1, 2, 3, '4', '5'])
                ->OnEquality(function ($Outer) { return $Outer; }, function ($Inner) { return $Inner; })
                ->To(function ($Outer, $Inner) {
                    return $Outer . '-' . $Inner;
                });
                
        $this->AssertMatchesValues($Traversable, ['1-1', '2-2', '3-3']);
    }
}