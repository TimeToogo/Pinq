<?php

namespace Pinq\Tests\Integration\Traversable;

class UnionTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatUnionWithSelfReturnsUniqueReindexedValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Unioned = $Traversable->Union($Traversable);
        
        $this->AssertMatches($Unioned, array_values(array_unique($Data)));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatUnionWithEmptyReturnsUniqueReindexedValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Unioned = $Traversable->Union(new \Pinq\Traversable());
        
        $this->AssertMatches($Unioned, array_values(array_unique($Data)));
    }
    
    /**
     * @dataProvider OneToTenTwice
     */
    public function testThatUnionRemovesDuplicateValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Unioned = $Traversable->Union(new \Pinq\Traversable());
        
        $this->AssertMatches($Unioned, array_values(array_unique($Data)));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatUnionUsesStrictEquality(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = [100 => '1', 101 => '2', 102 => '3'];
        $Unioned = $Traversable->Union($OtherData);
        
        $this->AssertMatches($Unioned, array_merge($Data, $OtherData));
    }
}
