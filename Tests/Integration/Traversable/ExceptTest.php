<?php

namespace Pinq\Tests\Integration\Traversable;

class ExceptTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Except($Traversable);
        
        $this->AssertMatches($Intersection, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Except(new \Pinq\Traversable());
        
        $this->AssertMatches($Intersection, $Data);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $ValuesWithSomeMatchingValues = new \Pinq\Traversable($OtherData);
        
        $Intersection = $Traversable->Except($ValuesWithSomeMatchingValues);
        
        $this->AssertMatches($Intersection, array_diff($Data, $OtherData));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $ValuesWithSomeMatchingKeys = new \Pinq\Traversable($OtherData);
        
        $Intersection = $Traversable->Except($ValuesWithSomeMatchingKeys);
        
        $this->AssertMatches($Intersection, array_diff($Data, $OtherData));
    }
}
