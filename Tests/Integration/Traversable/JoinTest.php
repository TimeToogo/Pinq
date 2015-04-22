<?php

namespace Pinq\Tests\Integration\Traversable;

class JoinTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable
                ->join([])
                    ->on(function ($i) {})
                    ->to(function ($k) {});
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->join([])->on($function)->to($function);
        });

        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->join([])->onEquality($function, $function)->to($function);
        });
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testJoinOnTrueProducesACartesianProduct(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join($data)
                    ->on(function () { return true; })
                    ->to(function ($outerValue, $innerValue) { return [$outerValue, $innerValue]; });

        $cartesianProduct = [];

        foreach ($data as $outerValue) {
            foreach ($data as $innerValue) {
                $cartesianProduct[] = [$outerValue, $innerValue];
            }
        }

        $this->assertMatchesValues($traversable, $cartesianProduct);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testJoinWillRewindCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([0 => 0])
                    ->on(function () { return true; })
                    ->to(function ($outerValue, $innerValue) { return $outerValue; });

        for ($count = 0; $count < 2; $count++) {
            $newData = [];
            foreach ($traversable as $value) {
                $newData[] = $value;
            }
            $this->assertSame(array_values($data), $newData);
        }
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testJoinOnFalseProducesEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join($data)
                    ->on(function () { return false; })
                    ->to(function ($outerValue, $innerValue) {
                        return [$outerValue, $innerValue];
                    });

        $this->assertMatches($traversable, []);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testJoinOnProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([1, 2, 3, '4', '5'])
                    ->on(function ($outer, $inner) { return $outer === $inner; })
                    ->to(function ($outer, $inner) {
                        return $outer . '-' . $inner;
                    });

        $this->assertMatchesValues($traversable, ['1-1', '2-2', '3-3']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testJoinOnEqualityProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([1, 2, 3, '4', '5'])
                    ->onEquality(function ($outer) { return $outer; }, function ($inner) { return $inner; })
                    ->to(function ($outer, $inner) {
                        return $outer . '-' . $inner;
                    });

        $this->assertMatchesValues($traversable, ['1-1', '2-2', '3-3']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testJoinWithTransformProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join(range(10, 20))
                    ->onEquality(function ($outer) { return $outer * 2; }, function ($inner) { return $inner; })
                    ->to(function ($outer, $inner) {
                        return $outer . ':' . $inner;
                    });

        $this->assertMatchesValues(
                $traversable,
                [
                    '5:10',
                    '6:12',
                    '7:14',
                    '8:16',
                    '9:18',
                    '10:20'
                ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testEqualityJoinOnKeysReturnsTheCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->indexBy(function ($value) { return $value; })
                ->join(array_fill_keys(range(15, 30), null))
                    ->onEquality(function ($o, $key) { return $key * 3; }, function ($i, $key) { return $key; })
                    ->to(function ($o, $i, $outerKey, $innerKey) {
                        return $outerKey . ':' . $innerKey;
                    });

        $this->assertMatchesValues(
                $traversable,
                [
                    '5:15',
                    '6:18',
                    '7:21',
                    '8:24',
                    '9:27',
                    '10:30'
                ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testJoinOnKeysAndValuesReturnsTheCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->indexBy(function ($value) { return $value; })
                ->join(range(0, 5, 0.5))
                    ->on(function ($outerValue, $innerValue, $outerKey) { return (double) ($outerKey / 2) === $innerValue; })
                    ->to(function ($outerValue, $innerValue, $outerKey) {
                        return $outerValue . ':' . $innerValue;
                    });

        $this->assertMatchesValues(
                $traversable,
                [
                    '1:0.5',
                    '2:1',
                    '3:1.5',
                    '4:2',
                    '5:2.5',
                    '6:3',
                    '7:3.5',
                    '8:4',
                    '9:4.5',
                    '10:5',
                ]);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatJoinDoesNotMaintainProjectedReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range(1, 20));

        $traversable
                ->append($data)
                ->join($traversable)
                    ->on(function () { return true; })
                    ->to(function & (&$i) { return $i; })
                ->iterate(function (&$i) { $i = null; });

        $this->assertSame(range(1, 20), $data);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatJoinWithDefaultWillSupplyDefaultElementWhenThereAreNoMatchingInnerElements(\Pinq\ITraversable $traversable, array $data)
    {
        $value = new \stdClass();
        $key = new \stdClass();

        $traversable = $traversable
                ->join(range(1, 10))
                    ->on(function () { return false; })
                    ->withDefault($value, $key)
                    ->to(function ($outer, $inner, $outerKey, $innerKey) {
                        return [$inner, $innerKey];
                    });

        $this->assertMatches($traversable, empty($data) ? [] : array_fill(0, count($data), [$value, $key]));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatJoinWithDefaultDoesNotSupplyDefaultElementWhenThereAreMatchingInnerElements(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([1])
                    ->on(function () { return true; })
                    ->withDefault(null, null)
                    ->to(function ($outer, $inner, $outerKey, $innerKey) {
                        return $inner;
                    });

        $this->assertMatches($traversable, empty($data) ? [] : array_fill(0, count($data), 1));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatJoinWithDefaultOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([2, 4, 8, 10, 16, 18])
                    ->on(function ($outer, $inner) { return $outer * 2 === $inner; })
                    ->withDefault('<DEFAULT>')
                    ->to(function ($outer, $inner) {
                        return $outer . ':' . $inner;
                    });

        $this->assertMatches($traversable, [
            '1:2',
            '2:4',
            '3:<DEFAULT>',
            '4:8',
            '5:10',
            '6:<DEFAULT>',
            '7:<DEFAULT>',
            '8:16',
            '9:18',
            '10:<DEFAULT>'
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatUnfilteredJoinToEmptyWithDefaultOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join([])
                ->withDefault('<DEFAULT>')
                ->to(function ($outer, $inner) {
                            return $outer . ':' . $inner;
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
                ->join($traversable)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->to(function ($outer, $inner) {
                        return $outer . ':' . $inner;
                    });

        $this->assertMatches($traversable, [
            '2:2',
            '4:4',
            '6:6',
            '8:8',
            '10:10'
        ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatOnEqualityWillNotMatchNullsAndUseDefault(\Pinq\ITraversable $traversable, array $data)
    {

        $traversable = $traversable
                ->join($traversable)
                ->onEquality(
                        function ($i) { return $i % 2 === 0 ? $i : null; },
                        function ($i) { return $i % 2 === 0 ? $i : null; })
                ->withDefault('<DEFAULT>')
                ->to(function ($outer, $inner) {
                            return $outer . ':' . $inner;
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
    public function testJoinToSelfWithInnerIndexBy(\Pinq\ITraversable $traversable)
    {
        $traversable = $traversable
                ->indexBy(function ($i) { return $i - 1; });

        $traversable = $traversable
                ->take(4)
                ->join($traversable)
                ->on(function ($outer, $inner) { return $outer > $inner; })
                ->to(function ($outer, $inner) {
                    return $outer . ':' . $inner;
                });

        $this->assertMatches($traversable, [
                '2:1',

                '3:1',
                '3:2',

                '4:1',
                '4:2',
                '4:3',
        ]);
    }
}
