<?php

namespace Pinq\Tests\Integration\Traversable;

class UniqueTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->unique();
    }

    public function notUniqueData()
    {
        $nonUnique = [
            'test' => 1,
            2,
            'test',
            4,
            4,
            2,
            1,
            4,
            5,
            6,
            3,
            7,
            'foo' => 23,
            7,
            3,
            46,
            'two' => 2,
            6,
            3,
            653,
            76457,
            5,
            'test',
            'test'
        ];

        return $this->everything() + $this->getImplementations($nonUnique);
    }

    /**
     * @dataProvider NotUniqueData
     */
    public function testThatUniqueValuesAreUnique(\Pinq\ITraversable $values, array $data)
    {
        $uniqueValues = $values->unique();

        $this->assertMatches($uniqueValues, array_unique($data, SORT_REGULAR));
    }

    /**
     * @dataProvider NotUniqueData
     */
    public function testThatUniqueValuesPreservesKeys(\Pinq\ITraversable $values, array $data)
    {
        $uniqueValuesArray = $values->unique()->asArray();

        $this->assertSame(
                array_keys(array_unique($data, SORT_REGULAR)),
                array_keys($uniqueValuesArray));
    }
}
