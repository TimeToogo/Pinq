<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectManyTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->SelectMany(function () { return []; });
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertThatExecutionIsDeferred([$Traversable, 'SelectMany']);
    }
    
    /**
     * @dataProvider TenRandomStrings
     */
    public function testThatSelectManyFlattensCorrectlyAndIgnoresKeys(\Pinq\ITraversable $Values, array $Data)
    {
        $ToCharacters = 'str_split';
        $Characters = $Values->SelectMany($ToCharacters);
        
        $this->AssertMatches($Characters, array_values(self::FlattenArrays(array_map($ToCharacters, $Data))));
    }
    
    private static function FlattenArrays(array $Arrays) 
    {
        return call_user_func_array('array_merge', array_map('array_values', $Arrays));
    }
}
