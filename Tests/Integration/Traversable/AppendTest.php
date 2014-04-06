<?php

namespace Pinq\Tests\Integration\Traversable;

class AppendTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithSelfReturnsMergedDataWithIgnoredKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Appended = $Traversable->Append($Traversable);
        
        $this->AssertMatches($Appended, array_merge(array_values($Data), array_values($Data)));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAppendtWithEmptyReturnsSameAsTheOriginalButIgnoresKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Appended = $Traversable->Append(new \Pinq\Traversable());
        
        $this->AssertMatches($Appended, array_values($Data));
    }
}
