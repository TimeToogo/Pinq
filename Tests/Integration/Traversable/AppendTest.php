<?php

namespace Pinq\Tests\Integration\Traversable;

class AppendTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->Append([]);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAppendWithSelfReturnsMergedDataWithReindexedKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Appended = $Traversable->Append($Traversable);
        
        $this->AssertMatches($Appended, array_merge(array_values($Data), array_values($Data)));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAppendtWithEmptyReturnsSameAsTheOriginalButReindexedKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $AppendedWithTraversable = $Traversable->Append(new \Pinq\Traversable());
        $AppendedWithArray = $Traversable->Append([]);
        $AppendedWithIterator = $Traversable->Append(new \ArrayObject([]));
        
        $this->AssertMatches($AppendedWithTraversable, array_values($Data));
        $this->AssertMatches($AppendedWithArray, array_values($Data));
        $this->AssertMatches($AppendedWithIterator, array_values($Data));
    }
}
