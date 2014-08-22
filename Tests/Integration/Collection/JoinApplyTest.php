<?php

namespace Pinq\Tests\Integration\Collection;

class JoinApplyTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred(function (callable $function) use ($collection) {
                $collection->join([1])->apply($function);
            });
        }
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferredForFilter(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred(function (callable $function) use ($collection) {
                $collection->join([1])->on($function)->apply($function);
            });
        }
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatApplyJoinPassesTheInnerAndOuterElements(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join([0 => 0])
                ->apply(function ($outer, $inner, $outerKey, $groupKey) use ($data) {
                    $this->assertSame($data[$outerKey], $outer);
                    $this->assertSame($inner, 0);
                    $this->assertSame($groupKey, 0);
                });
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join([2, 3])
                ->apply(function (&$outer, $inner) {
                    $outer *= $inner;
                });

        $this->assertMatchesValues($collection, [
            1 * 2 * 3,
            2 * 2 * 3,
            3 * 2 * 3,
            4 * 2 * 3,
            5 * 2 * 3,
            6 * 2 * 3,
            7 * 2 * 3,
            8 * 2 * 3,
            9 * 2 * 3,
            10 * 2 * 3,
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatFilteredApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join(range(1, 10))
                ->on(function ($outer, $inner) { return $inner <= $outer; })
                ->apply(function (&$outer, $inner) {
                    $outer *= $inner;
                });

        $this->assertMatchesValues($collection, [
            1 * 1,
            2 * 1 * 2,
            3 * 1 * 2 * 3,
            4 * 1 * 2 * 3 * 4,
            5 * 1 * 2 * 3 * 4 * 5,
            6 * 1 * 2 * 3 * 4 * 5 * 6,
            7 * 1 * 2 * 3 * 4 * 5 * 6 * 7,
            8 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8,
            9 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8 * 9,
            10 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8 * 9 * 10,
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatAppendedFilteredApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $appendedReference = 3;

        $collection
                ->append([&$appendedReference])
                ->join(range(1, 10))
                ->on(function ($outer, $inner) { return $inner <= $outer; })
                ->apply(function (&$outer, $inner) {
                    $outer *= $inner;
                });

        $this->assertMatchesValues($collection, [
            1 * 1,
            2 * 1 * 2,
            3 * 1 * 2 * 3,
            4 * 1 * 2 * 3 * 4,
            5 * 1 * 2 * 3 * 4 * 5,
            6 * 1 * 2 * 3 * 4 * 5 * 6,
            7 * 1 * 2 * 3 * 4 * 5 * 6 * 7,
            8 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8,
            9 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8 * 9,
            10 * 1 * 2 * 3 * 4 * 5 * 6 * 7 * 8 * 9 * 10,
        ]);

        $this->assertSame($appendedReference, 3 * 1 * 2 * 3);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatEqualityFilteredApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join(range(1, 10))
                ->onEquality(function ($outer) { return $outer; }, function ($inner) { return $inner; })
                ->apply(function (&$outer, $inner) {
                    $outer *= $inner;
                });

        $this->assertMatchesValues($collection, [
            1 * 1,
            2 * 2,
            3 * 3,
            4 * 4,
            5 * 5,
            6 * 6,
            7 * 7,
            8 * 8,
            9 * 9,
            10 * 10,
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatApplyJoinWithDefaultValueOperatedCorrectly(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join(range(-1, -10, -2))
                ->on(function ($outer, $inner) { return -$outer === $inner; })
                ->withDefault('<EVEN>')
                ->apply(function (&$outer, $inner) {
                    $outer .= ':' . $inner;
                });

        $this->assertMatchesValues($collection, [
            '1:-1',
            '2:<EVEN>',
            '3:-3',
            '4:<EVEN>',
            '5:-5',
            '6:<EVEN>',
            '7:-7',
            '8:<EVEN>',
            '9:-9',
            '10:<EVEN>',
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatOnEqualityWillNotMatchNulls(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->join($collection)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->apply(function (&$outer, $inner) {
                    $outer .= ':' . $inner;
                });

        $this->assertMatches($collection, [
            1,
            '2:2',
            3,
            '4:4',
            5,
            '6:6',
            7,
            '8:8',
            9,
            '10:10'
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatOnEqualityWillNotMatchNullsAndUseDefault(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin($collection)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->withDefault('<DEFAULT>')
                ->apply(function (&$outer, \Pinq\ITraversable $innerGroup) {
                    $outer .= ':' . $innerGroup->implode('-');
                });

        $this->assertMatches($collection, [
            '1:<DEFAULT>',
            '2:2',
            '3:<DEFAULT>',
            '4:4',
            '5:<DEFAULT>',
            '6:6',
            '7:<DEFAULT>',
            '8:8',
            '9:<DEFAULT>',
            '10:10'
        ]);
    }
}
