<?php 

namespace Pinq\Tests\Integration\Traversable;

class SelectManyTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->selectMany(function () {
            return [];
        });
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'SelectMany']);
    }
    
    /**
     * @dataProvider TenRandomStrings
     */
    public function testThatSelectManyFlattensCorrectlyAndIgnoresKeys(\Pinq\ITraversable $values, array $data)
    {
        $toCharacters = 'str_split';
        $characters = $values->selectMany($toCharacters);
        $this->assertMatches(
                $characters,
                array_values(self::flattenArrays(array_map($toCharacters, $data))));
    }
    
    private static function flattenArrays(array $arrays)
    {
        return call_user_func_array('array_merge', array_map('array_values', $arrays));
    }
}