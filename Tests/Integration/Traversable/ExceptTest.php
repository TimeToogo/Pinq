<?php

namespace Pinq\Tests\Integration\Traversable;

class ExceptTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Except = $Traversable->Except($Traversable);
        
        $this->AssertMatches($Except, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Except = $Traversable->Except(new \Pinq\Traversable());
        
        $this->AssertMatches($Except, $Data);
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $ValuesWithSomeMatchingValues = new \Pinq\Traversable($OtherData);
        
        $Except = $Traversable->Except($ValuesWithSomeMatchingValues);
        
        $this->AssertMatches($Except, array_diff($Data, $OtherData));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $ValuesWithSomeMatchingKeys = new \Pinq\Traversable($OtherData);
        
        $Except = $Traversable->Except($ValuesWithSomeMatchingKeys);
        
        $this->AssertMatches($Except, array_diff($Data, $OtherData));
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatExceptUsesStrictEquality(\Pinq\ITraversable $Traversable, array $Data)
    {
        $ValuesWithSomeMatchingKeys = new \Pinq\Traversable(array_map('strval', $Data));
        
        $Except = $Traversable->Except($ValuesWithSomeMatchingKeys);
        
        $this->AssertMatches($Except, $Data);
    }
}
