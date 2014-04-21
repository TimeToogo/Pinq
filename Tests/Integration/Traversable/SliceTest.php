<?php

namespace Pinq\Tests\Integration\Traversable;

class SliceTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->Slice(1, 2);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatSkipRemovesCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $WithoutFirstFiveElements = $Traversable->Skip(5);
        
        $this->AssertMatches($WithoutFirstFiveElements, array_slice($Data, 5, null, true));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatTakeGetsTheCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $FirstFiveElements = $Traversable->Take(5);
        
        $this->AssertMatches($FirstFiveElements, array_slice($Data, 0, 5, true));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatTakeZeroReturnsEmptyArray(\Pinq\ITraversable $Traversable, array $Data)
    {
        $NoNumbers = $Traversable->Take(0);
        
        $this->AssertMatches($NoNumbers, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatSkipZeroReturnsOriginalArray(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Values = $Traversable->Skip(0);
        
        $this->AssertMatches($Values, $Data);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatSlicingReturnsTheCorrectSegmentOfDataAndPreservesKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Values = $Traversable->Slice(3, 2);
        
        $this->AssertMatches($Values, array_slice($Data, 3, 2, true));
    }
}
