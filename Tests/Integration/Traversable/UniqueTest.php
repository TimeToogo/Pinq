<?php

namespace Pinq\Tests\Integration\Traversable;

class UniqueTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->Unique();
    }
    
    public function NotUniqueData()
    {
        $NonUnique = ['test' => 1,2, 'test',4,4,2,1,4,5,6,3,7,'foo' => 23,7,3,46, 'two' => 2,6,3,653,76457,5 ,'test', 'test'];
        
        return $this->Everything() + $this->GetImplementations($NonUnique);
    }
    
    /**
     * @dataProvider NotUniqueData
     */
    public function testThatUniqueValuesAreUnique(\Pinq\ITraversable $Values, array $Data)
    {
        $UniqueValues = $Values->Unique();
        
        $this->AssertMatches($UniqueValues, array_unique($Data, SORT_REGULAR));
    }
    
    /**
     * @dataProvider NotUniqueData
     */
    public function testThatUniqueValuesPreservesKeys(\Pinq\ITraversable $Values, array $Data)
    {
        $UniqueValuesArray = $Values->Unique()->AsArray();
        
        $this->assertSame(array_keys(array_unique($Data, SORT_REGULAR)), array_keys($UniqueValuesArray));
    }
}
