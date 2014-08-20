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
     * @dataProvider assocMixedValues
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

    /**
     * @dataProvider emptyData
     */
    public function testThatSelectManyMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range(100, 1, -1));

        $traversable
                ->append($data)
                ->groupBy(function ($i) { return $i % 3; })
                ->selectMany(function (\Pinq\ITraversable $group) { return $group; })
                ->iterate(function (&$i) { $i *= 10; });

        $this->assertSame(range(1000, 10, -10), $data);
    }
}
