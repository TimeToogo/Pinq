<?php

namespace Pinq\Tests\Integration\Collection;

class GroupJoinApplyTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred(function (callable $function) use ($collection) {
                $collection->groupJoin([1])->apply($function);
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
                $collection->groupJoin([1])->on($function)->apply($function);
            });

            $this->assertThatExecutionIsNotDeferred(function (callable $function) use ($collection) {
                $collection->groupJoin([1])->onEquality($function, $function)->apply($function);
            });
        }
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatApplyJoinPassesTheInnerAndOuterElements(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin([1 => 0])
                ->apply(function ($outer, \Pinq\ITraversable $group, $outerKey, $groupKey) use ($data) {
                    $this->assertSame($data[$outerKey], $outer);
                    $this->assertSame($group->asArray(), [1 => 0]);
                    $this->assertSame($groupKey, 0);
                });
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin([1, 2, 3])
                ->apply(function (&$outer, \Pinq\ITraversable $group) {
                    $outer = $group->implode(':', function ($i) use ($outer) { return $i * $outer; });
                });

        $this->assertMatchesValues($collection, [
            '1:2:3',
            '2:4:6',
            '3:6:9',
            '4:8:12',
            '5:10:15',
            '6:12:18',
            '7:14:21',
            '8:16:24',
            '9:18:27',
            '10:20:30',
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatFilteredApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin(range(1, 10))
                ->on(function ($outer, $inner) { return $inner <= $outer; })
                ->apply(function (&$outer, \Pinq\ITraversable $innerGroup) {
                    $outer *= $innerGroup->sum();
                });

        $this->assertMatchesValues($collection, [
            1 * (1),
            2 * (1 + 2),
            3 * (1 + 2 + 3),
            4 * (1 + 2 + 3 + 4),
            5 * (1 + 2 + 3 + 4 + 5),
            6 * (1 + 2 + 3 + 4 + 5 + 6),
            7 * (1 + 2 + 3 + 4 + 5 + 6 + 7),
            8 * (1 + 2 + 3 + 4 + 5 + 6 + 7 + 8),
            9 * (1 + 2 + 3 + 4 + 5 + 6 + 7 + 8 + 9),
            10 * (1 + 2 + 3 + 4 + 5 + 6 + 7 + 8 + 9 + 10),
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatEqualityFilteredApplyJoinOperatesOnOriginalCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin(range(1, 10))
                ->onEquality(function ($outer) { return $outer; }, function ($inner) { return $inner; })
                ->apply(function (&$outer, \Pinq\ITraversable $innerGroup) {
                    $outer *= $innerGroup->first();
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
    public function testThatApplyGroupJoinWithDefaultValueOperatedCorrectly(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->groupJoin(range(1, 20, 2))
                ->on(function ($outer, $inner) { return $outer % 3 !== 0 && $outer * 2 >= $inner; })
                ->withDefault('<MUL3>')
                ->apply(function (&$outer, \Pinq\ITraversable $innerGroup) {
                    $outer .= ':' . $innerGroup->implode(',');
                });

        $this->assertMatchesValues($collection, [
            '1:1',
            '2:1,3',
            '3:<MUL3>',
            '4:1,3,5,7',
            '5:1,3,5,7,9',
            '6:<MUL3>',
            '7:1,3,5,7,9,11,13',
            '8:1,3,5,7,9,11,13,15',
            '9:<MUL3>',
            '10:1,3,5,7,9,11,13,15,17,19',
        ]);
    }
    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinApplyToSelfWithInnerIndexBy(\Pinq\ICollection $collection)
    {
        $inner = $collection
                ->indexBy(function ($i) { return $i - 1; })
                ->select(function ($i) { return (int)$i; });

        $collection
                ->groupJoin($inner)
                ->on(function ($outer, $inner) { return $outer > $inner; })
                ->apply(function (&$outer, \Pinq\ITraversable $group) {
                    $outer .= ':' . $group->implode(',');
                });

        $this->assertMatches($collection, [
                0 => '1:',
                1 => '2:1',
                2 => '3:1,2',
                3 => '4:1,2,3',
                4 => '5:1,2,3,4',
                5 => '6:1,2,3,4,5',
                6 => '7:1,2,3,4,5,6',
                7 => '8:1,2,3,4,5,6,7',
                8 => '9:1,2,3,4,5,6,7,8',
                9 => '10:1,2,3,4,5,6,7,8,9',
        ]);
    }
}
