<?php

namespace Pinq\Tests\Integration\Traversable;

class UniqueTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
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

        return array_merge($this->everything(), $this->getImplementations($nonUnique));
    }

    /**
     * @dataProvider notUniqueData
     */
    public function testThatUniqueValuesAreUnique(\Pinq\ITraversable $values, array $data)
    {
        $uniqueValues = $values->unique();

        $uniqueData = [];
        foreach ($data as $key => $value) {
            if (!in_array($value, $uniqueData, true)) {
                $uniqueData[$key] = $value;
            }
        }

        $this->assertMatches($uniqueValues, $uniqueData);
    }

    /**
     * @dataProvider notUniqueData
     */
    public function testThatUniqueUsesTheFirstFoundValueForDuplicates(\Pinq\ITraversable $values, array $data)
    {
        $value1 = [1,90 => 2,3,'t' => 'foo', null, true];
        $value2 = [1,90 => 2,3,'t' => 'foo', null, true];

        $uniqueValues = $values
                ->take(0)
                ->append([&$value1, &$value2])
                ->unique();

        $this->assertReferenceEquals($value1, $uniqueValues->asArray()[0]);
    }

    /**
     * @dataProvider notUniqueData
     */
    public function testThatUniqueValuesPreservesKeys(\Pinq\ITraversable $values, array $data)
    {
        $uniqueValuesArray = $values->unique()->asArray();

        $uniqueData = [];
        foreach ($data as $key => $value) {
            if (!in_array($value, $uniqueData, true)) {
                $uniqueData[$key] = $value;
            }
        }

        $this->assertSame(
                array_keys($uniqueData),
                array_keys($uniqueValuesArray));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatUniqueMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(array_merge(range(1, 10, 2), range(1, 10)));

        $traversable
                ->append($data) //1,3,5,7,9,1,2,3,4,5,6,7,8,9,10
                ->unique()      //1,3,5,7,9,2,4,6,8,10
                ->iterate(function (&$i) { $i *= 10; });

        $this->assertSame([10, 30, 50, 70, 90, 1, 20, 3, 40, 5, 60, 7, 80, 9, 100], $data);
    }
}
