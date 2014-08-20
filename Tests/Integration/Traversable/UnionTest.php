<?php

namespace Pinq\Tests\Integration\Traversable;

class UnionTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->union([]);
    }

    /**
     * @dataProvider assocStrings
     */
    public function testThatUnionWithSelfReturnsUniqueReindexedValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union($traversable);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider assocStrings
     */
    public function testThatUnionWithEmptyReturnsUniqueReindexedValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union([]);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatUnionRemovesDuplicateValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union([]);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatUnionUsesStrictEquality(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = [100 => '1', 101 => '2', 102 => '3'];
        $unioned = $traversable->union($otherData);

        $this->assertMatches($unioned, array_merge($data, $otherData));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatUnionMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('a', 'f'));

        $traversable
                ->union($data)
                ->iterate(function (&$i) { $i = "$i-"; });

        $this->assertSame('a-b-c-d-e-f-', implode('', $data));
    }
}
