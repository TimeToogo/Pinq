<?php

namespace Pinq\Tests\Integration\Traversable;

class UnionTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatUnionWithSelfReturnsTheSameAsTheOriginal(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Unioned = $Traversable->Union($Traversable);
        
        $this->AssertMatches($Unioned, $Data);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatUnionWithEmptyReturnsTheSameAsTheOriginal(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Unioned = $Traversable->Union(new \Pinq\Traversable());
        
        $this->AssertMatches($Unioned, $Data);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatUnionWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $ValuesWithSomeMatchingValues = new \Pinq\Traversable(['test' => 1, 'anotherkey' => 3, 1000 => 5]);
        $Unioned = $Traversable->Union($ValuesWithSomeMatchingValues);
        
        $this->AssertMatches($Unioned, $Data);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatUnionWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $ValuesWithSomeMatchingKeys = new \Pinq\Traversable([0 => 'test', 2 => 0.01, 5 => 4]);
        $Unioned = $Traversable->Union($ValuesWithSomeMatchingKeys);
        
        $this->AssertMatches($Unioned, $Data);
    }
}
