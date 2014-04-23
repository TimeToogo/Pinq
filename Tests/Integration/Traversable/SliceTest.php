<?php

namespace Pinq\Tests\Integration\Traversable;

class SliceTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->slice(1, 2);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatSkipRemovesCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $withoutFirstFiveElements = $traversable->skip(5);

        $this->assertMatches(
                $withoutFirstFiveElements,
                array_slice($data, 5, null, true));
    }

    /**
     * @dataProvider Everything
     */
    public function testThatTakeGetsTheCorrectAmountOfElementsFromTheStartAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $firstFiveElements = $traversable->take(5);

        $this->assertMatches(
                $firstFiveElements,
                array_slice($data, 0, 5, true));
    }

    /**
     * @dataProvider Everything
     */
    public function testThatTakeZeroReturnsEmptyArray(\Pinq\ITraversable $traversable, array $data)
    {
        $noNumbers = $traversable->take(0);

        $this->assertMatches($noNumbers, []);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatSkipZeroReturnsOriginalArray(\Pinq\ITraversable $traversable, array $data)
    {
        $values = $traversable->skip(0);

        $this->assertMatches($values, $data);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatSlicingReturnsTheCorrectSegmentOfDataAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $values = $traversable->slice(3, 2);

        $this->assertMatches($values, array_slice($data, 3, 2, true));
    }
}
