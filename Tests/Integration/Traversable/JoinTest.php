<?php

namespace Pinq\Tests\Integration\Traversable;

class JoinTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->join([])->on(function ($i) {

        })->to(function ($k) {

        });
    }

    /**
     * @dataProvider everything
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
     * @dataProvider everything
     */
    public function testJoinOnTrueProducesACartesianProduct(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable->join($data)->on(function () {
                    return true;
                })->to(function ($outerValue, $innerValue) {
                    return [$outerValue, $innerValue];
                });
        $cartesianProduct = [];

        foreach ($data as $outerValue) {
            foreach ($data as $innerValue) {
                $cartesianProduct[] = [$outerValue, $innerValue];
            }
        }

        $this->assertMatchesValues($traversable, $cartesianProduct);
    }

    /**
     * @dataProvider everything
     */
    public function testJoinOnFalseProducesEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->join($data)->on(function () { return false; })
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
        $traversable = $traversable->join([1, 2, 3, '4', '5'])
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
        $traversable = $traversable->join([1, 2, 3, '4', '5'])
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
        $traversable = $traversable->join(range(10, 20))
                ->onEquality(function ($outer) { return $outer * 2; }, function ($inner) { return $inner;})
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
}
