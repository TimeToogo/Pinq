<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupJoinTest extends TraversableTest
{

    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->groupJoin([])->on(function ($i) { })->to(function ($k) { });
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->groupJoin([])->to($function);
        });

        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->groupJoin([])->on($function)->to($function);
        });

        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->groupJoin([])->onEquality($function, $function)->to($function);
        });
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testCalledWithCorrectValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable
                ->groupJoin([0 => 0])
                    ->on(function ($outer, $inner, $outerKey, $innerKey) use ($data) {
                        $this->assertSame($data[$outerKey], $outer);
                        $this->assertSame($inner, 0);
                        $this->assertSame($innerKey, 0);

                        return true;
                    })
                    ->to(function ($outer, \Pinq\ITraversable $group, $outerKey, $groupKey) use ($data) {
                        $this->assertSame($data[$outerKey], $outer);
                        $this->assertSame($group->asArray(), [0 => 0]);
                        $this->assertSame($groupKey, 0);
                    })
                    ->asArray();

        $traversable
                ->groupJoin([0 => 0])
                    ->onEquality(
                    function ($outer, $outerKey) use ($data) {
                        $this->assertSame($data[$outerKey], $outer);

                        return 'group Key';
                    },
                    function ($inner, $innerKey) {
                        $this->assertSame($inner, 0);
                        $this->assertSame($innerKey, 0);

                        return 'group Key';
                    })
                    ->to(function ($outer, \Pinq\ITraversable $group, $outerKey, $groupKey) use ($data) {
                        $this->assertSame($data[$outerKey], $outer);
                        $this->assertSame($group->asArray(), [0 => 0]);
                        $this->assertSame($groupKey, 'group Key');
                    })
                    ->asArray();
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testGroupJoinOnTrueProducesTheCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin($data)
                    ->on(function () { return true; })
                    ->to(function ($outerValue, \Pinq\ITraversable $group) {
                        return [$outerValue, $group->asArray()];
                    });

        $correctResult = [];

        foreach ($data as $outerValue) {
            $correctResult[] = [$outerValue, $data];
        }

        $this->assertMatchesValues($traversable, $correctResult);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testGroupJoinOnFalseProducesEmptyLeftJoin(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable
                ->groupJoin($data)
                    ->on(function () { return false; })
                    ->to(function ($outerValue, \Pinq\ITraversable $group) {
                        return [$outerValue, $group->asArray()];
                    });

        $emptyLeftJoin = [];

        foreach ($data as $value) {
            $emptyLeftJoin[] = [$value, []];
        }

        $this->assertMatchesValues($traversable, $emptyLeftJoin);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([1,2,2,3,'4','5'])
                    ->on(function ($outer, $inner) { return $outer === $inner; })
                    ->to(function ($outer, \Pinq\ITraversable $values) {
                        return $outer . '-' . $values->implode('-');
                    });

        $this->assertMatchesValues($traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinOnEqualityProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([1, 2, 2, 3, '4', '5'])
                    ->onEquality(function ($outer) { return $outer; }, function ($inner) { return $inner; })
                    ->to(function ($outer, \Pinq\ITraversable $values) { return $outer . '-' . $values->implode('-'); });

        $this->assertMatchesValues($traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinWithStringsProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable
                ->groupJoin(['foo', 'bar', 'baz', 'tear', 'cow', 'tripod', 'whisky', 'sand', 'which'])
                    ->onEquality(function ($outer) { return $outer;}, 'strlen')
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                        return $outer . ':' . $innerGroup->implode('|');
                    });

        $this->assertMatchesValues(
                $traversable,
                [
                    '1:',
                    '2:',
                    '3:foo|bar|baz|cow',
                    '4:tear|sand',
                    '5:which',
                    '6:tripod|whisky',
                    '7:',
                    '8:',
                    '9:',
                    '10:'
                ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinWithGreaterThanProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin($traversable)
                    ->on(function ($outer, $inner) { return $outer >= $inner; })
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                        return $outer . ':' . $innerGroup->implode('|');
                    });

        $this->assertMatchesValues(
                $traversable,
                [
                    '1:1',
                    '2:1|2',
                    '3:1|2|3',
                    '4:1|2|3|4',
                    '5:1|2|3|4|5',
                    '6:1|2|3|4|5|6',
                    '7:1|2|3|4|5|6|7',
                    '8:1|2|3|4|5|6|7|8',
                    '9:1|2|3|4|5|6|7|8|9',
                    '10:1|2|3|4|5|6|7|8|9|10'
                ]);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatGroupJoinDoesNotMaintainProjectedValueReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range(1, 20));

        $traversable
                ->append($data)
                ->groupJoin($traversable)
                    ->on(function () { return true; })
                    ->to(function & (&$i) { return $i; });

        $this->assertSame(range(1, 20), $data);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatGroupJoinMaintainsGroupedDataReferences(\Pinq\ITraversable $traversable)
    {
        $joinData = $this->makeRefs(range(1, 100));

        $traversable
                ->append(range(1, 100, 10))
                ->groupJoin($joinData)
                    ->on(function ($o, $i) { return (int) ($o / 10) === (int) ($i / 10); })
                    ->to(function ($o, \Pinq\ITraversable $group) {
                        return $group;
                    })
                [3]
                ->iterate(function (&$i) { $i *= 10; });

        $this->assertSame(array_merge(range(1, 29), range(300, 390, 10), range(40, 100)), $joinData);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatGroupJoinOnEqualityMaintainsGroupedDataReferences(\Pinq\ITraversable $traversable)
    {
        $joinData = $this->makeRefs(range(1, 100));

        $traversable
                ->append(range(1, 100, 10))
                ->groupJoin($joinData)
                    ->onEquality(function ($o) { return (int) ($o / 10); }, function ($i) { return (int) ($i / 10); })
                    ->to(function ($o, \Pinq\ITraversable $group) {
                        return $group;
                    })
                [3]
                ->iterate(function (&$i) { $i *= 10; });

        $this->assertSame(array_merge(range(1, 29), range(300, 390, 10), range(40, 100)), $joinData);
    }

    /**
     * @dataProvider everything
     */
    public function testThatGroupJoinWithDefaultWillSupplyDefaultElementWhenThereAreNoMatchingInnerElements(\Pinq\ITraversable $traversable, array $data)
    {
        $value = new \stdClass();
        $key = new \stdClass();

        $traversable = $traversable
                ->groupJoin(range(1, 10))
                    ->on(function () { return false; })
                    ->withDefault($value, $key)
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) use ($key) {
                        return $innerGroup[$key];
                    });

        $this->assertMatches($traversable, empty($data) ? [] : array_fill(0, count($data), $value));
    }

    /**
     * @dataProvider everything
     */
    public function testThatGroupJoinWithDefaultDoesNotSupplyDefaultElementWhenThereAreMatchingInnerElements(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([1])
                    ->on(function () { return true; })
                    ->withDefault(null, null)
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                        return $innerGroup->asArray();
                    });

        $this->assertMatches($traversable, empty($data) ? [] : array_fill(0, count($data), [1]));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatGroupJoinWithDefaultOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([1, 4, 9, 16, 25, 36, 49, 64, 81, 100])
                    ->on(function ($outer, $inner) { return $outer % 2 === 0 && $outer * $outer >= $inner; })
                    ->withDefault('<ODD>')
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                        return $outer . ':' . $innerGroup->implode(',');
                    });

        $this->assertMatches($traversable, [
            '1:<ODD>',
            '2:1,4',
            '3:<ODD>',
            '4:1,4,9,16',
            '5:<ODD>',
            '6:1,4,9,16,25,36',
            '7:<ODD>',
            '8:1,4,9,16,25,36,49,64',
            '9:<ODD>',
            '10:1,4,9,16,25,36,49,64,81,100',
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatUnfilteredJoinToEmptyWithDefaultOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([])
                    ->withDefault('<DEFAULT>')
                    ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                        return $outer . ':' . $innerGroup->implode('');
                    });

        $this->assertMatches($traversable, [
            '1:<DEFAULT>',
            '2:<DEFAULT>',
            '3:<DEFAULT>',
            '4:<DEFAULT>',
            '5:<DEFAULT>',
            '6:<DEFAULT>',
            '7:<DEFAULT>',
            '8:<DEFAULT>',
            '9:<DEFAULT>',
            '10:<DEFAULT>'
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatOnEqualityWillNotMatchNulls(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin($traversable)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                    return $outer . ':' . $innerGroup->implode('-');
                });

        $this->assertMatches($traversable, [
            '1:',
            '2:2',
            '3:',
            '4:4',
            '5:',
            '6:6',
            '7:',
            '8:8',
            '9:',
            '10:10'
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatOnEqualityWillNotMatchNullsAndUseDefault(\Pinq\ITraversable $traversable, array $data)
    {

        $traversable = $traversable
                ->groupJoin($traversable)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->withDefault('<DEFAULT>')
                ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                    return $outer . ':' . $innerGroup->implode('-');
                });

        $this->assertMatches($traversable, [
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

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinToSelfWithInnerIndexBy(\Pinq\ITraversable $traversable)
    {
        $traversable = $traversable
                ->indexBy(function ($i) { return $i - 1; });

        $traversable = $traversable
                ->groupJoin($traversable)
                ->on(function ($outer, $inner) { return $outer > $inner; })
                ->to(function ($outer, \Pinq\ITraversable $group) {
                    return $outer . ':' . $group->implode(',');
                });

        $this->assertMatches($traversable, [
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
