<?php

namespace Pinq\Tests\Integration\Traversable;

class DifferenceTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->difference([]);
    }

    /**
     * @dataProvider everything
     */
    public function testThatDifferenceWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->difference($traversable);

        $this->assertMatches($intersection, []);
    }

    /**
     * @dataProvider assocStrings
     */
    public function testThatDifferenceWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->difference([]);

        $this->assertMatches($intersection, array_unique($data));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatDifferenceWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $intersection = $traversable->difference($otherData);

        $this->assertMatches($intersection, array_diff($data, $otherData));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatDifferenceWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $intersection = $traversable->difference($otherData);

        $this->assertMatches($intersection, array_diff($data, $otherData));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatDifferenceMaintainsReferences(\Pinq\ITraversable $traversable, array $data)
    {
        $ref = 5;
        $traversable = $traversable->append([&$ref])->difference($traversable);

        $this->assertReferenceEquals($ref, $traversable->asArray()[0]);
    }
}
