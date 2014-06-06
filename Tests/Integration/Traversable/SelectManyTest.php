<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectManyTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->selectMany(function () {
            return [];
        });
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'selectMany']);
    }
    
    /**
     * @dataProvider everything
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'selectMany'], $data, []);
    }

    /**
     * @dataProvider tenRandomStrings
     */
    public function testThatSelectManyFlattensCorrectlyAndIgnoresKeys(\Pinq\ITraversable $values, array $data)
    {
        $characters = $values->selectMany(function ($i) { return str_split($i); });

        $this->assertMatches(
                $characters,
                array_values(self::flattenArrays(array_map('str_split', $data))));
    }

    private static function flattenArrays(array $arrays)
    {
        return call_user_func_array('array_merge', array_map('array_values', $arrays));
    }
}
