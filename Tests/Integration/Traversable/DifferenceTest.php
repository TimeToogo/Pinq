<?php

namespace Pinq\Tests\Integration\Traversable;

class DifferenceTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatDifferenceWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Difference($Traversable);
        
        $this->AssertMatches($Intersection, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatDifferenceWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Difference(new \Pinq\Traversable());
        
        $this->AssertMatches($Intersection, array_unique($Data));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatDifferenceWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $ValuesWithSomeMatchingValues = new \Pinq\Traversable($OtherData);
        
        $Intersection = $Traversable->Difference($ValuesWithSomeMatchingValues);
        
        $this->AssertMatches($Intersection, array_diff($Data, $OtherData));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatDifferenceWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $ValuesWithSomeMatchingKeys = new \Pinq\Traversable($OtherData);
        
        $Intersection = $Traversable->Difference($ValuesWithSomeMatchingKeys);
        
        $this->AssertMatches($Intersection, array_diff($Data, $OtherData));
    }
}
