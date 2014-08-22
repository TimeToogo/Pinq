<?php

namespace Pinq\Tests\Integration\Traversable;

class SliceTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->slice(1, 2);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatSkipRemovesCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $withoutFirstFiveElements = $traversable->skip(5);

        $this->assertMatches(
                $withoutFirstFiveElements,
                array_slice($data, 5, null, true));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatTakeGetsTheCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $firstFiveElements = $traversable->take(5);

        $this->assertMatches(
                $firstFiveElements,
                array_slice($data, 0, 5, true));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatTakeZeroReturnsEmptyArray(\Pinq\ITraversable $traversable, array $data)
    {
        $noNumbers = $traversable->take(0);

        $this->assertMatches($noNumbers, []);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatSkipZeroReturnsOriginalArray(\Pinq\ITraversable $traversable, array $data)
    {
        $values = $traversable->skip(0);

        $this->assertMatches($values, $data);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatSlicingReturnsTheCorrectSegmentOfDataAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $values = $traversable->slice(3, 2);

        $this->assertMatches($values, array_slice($data, 3, 2, true));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatSliceMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('Z', 'A'));

        $traversable
                ->append($data) //ZYXWVUTSRQPONMLKJIHGFEDCBA
                ->skip(10)      //PONMLKJIHGFEDCBA
                ->take(7)       //PONMLKJ
                ->slice(5, 3)   //KJ
                ->iterate(function (&$i) { $i = "-$i$i-"; });

        $this->assertSame('ZYXWVUTSRQPONML-KK--JJ-IHGFEDCBA', implode('', $data));
    }
}
