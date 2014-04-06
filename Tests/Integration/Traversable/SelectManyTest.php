<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectManyTest extends TraversableTest
{
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
        return call_user_func_array('array_merge', $Arrays);
    }
}
