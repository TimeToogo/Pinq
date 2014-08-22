<?php

namespace Pinq\Tests\Integration\Traversable;

class MutableTraversableSemanticsTest extends TraversableTest
{
    /**
     * @dataProvider oneToTen
     */
    public function testThatQueryUpdatesWhenValuesAreMutated(\Pinq\ITraversable $traversable, array $data)
    {
        $mutableValues = new \ArrayObject([2, 4, 6, 8, 10]);

        $query = $traversable
                ->whereIn($mutableValues)
                ->orderByDescending(function ($i) { return $i; })
                ->groupJoin(range(1, 10, 2))
                    ->on(function ($i, $v) { return $v < $i; })
                    ->to(function ($i, \Pinq\ITraversable $nums) {
                        return $i . ':' . $nums->implode(',');
                    });

        $this->assertMatchesValues($query, [
            '10:1,3,5,7,9',
            '8:1,3,5,7',
            '6:1,3,5',
            '4:1,3',
            '2:1',
        ]);

        $mutableValues->exchangeArray([1, 3, 5, 7, 9]);

        $this->assertMatchesValues($query, [
            '9:1,3,5,7',
            '7:1,3,5',
            '5:1,3',
            '3:1',
            '1:',
        ]);
    }

    /**
     * @dataProvider theImplementations
     */
    public function testWithNonDeterministicQueryValuesAreNeverCached(\Pinq\ITraversable $traversable, array $data)
    {
        $query = $traversable
                ->append(range(1, 50))
                //Random where condition
                ->where(function ($i) { return (bool) mt_rand(0, 1); })
                ->orderByDescending(function ($i) { return (int) ($i / 10); })
                ->thenByAscending(function ($i) { return $i; })
                ->slice(0, 50)
                ->select(function ($i) { return ++$i; })
                ->groupBy(function ($i) { return $i % 3; })
                ->selectMany(function (\Pinq\ITraversable $group) { return $group; })
                ->difference(range(15, 30, 2))
                ->groupJoin(range(2, 10))
                    ->on(function ($i, $d) { return $i % $d === 0; })
                    ->to(function ($i, \Pinq\ITraversable $factors) {
                        return $i . ':' . $factors->implode(',');
                    });

        $this->assertNotEquals($query->asArray(), $query->asArray());
    }
}
