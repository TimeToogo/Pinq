<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

use Pinq\IQueryable;

class NumericTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function oneToAHundred()
    {
        return $this->getImplementations(range(1, 100));
    }

    /**
     * @dataProvider oneToAHundred
     */
    public function testOrderByTensThenDescending(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->orderByAscending(function ($i) { return (int) ($i / 10); })
                ->thenByDescending(function ($i) { return $i; });

        $equivalentArray = [];
        $array = [];

        foreach ($data as $key => $value) {
            if ($value % 10 === 0) {
                $equivalentArray += array_reverse($array, true);
                $array = [];
            }

            $array[$key] = $value;
        }

        $equivalentArray += array_reverse($array, true);
        $this->assertMatches($traversable, $equivalentArray);
    }

    /**
     * @dataProvider oneToAHundred
     */
    public function testSimpleAggregation(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(array_sum($data), $traversable->sum());

        $this->assertSame(array_sum($data) / count($data), $traversable->average());
    }

    /**
     * @dataProvider oneToAHundred
     */
    public function testComplexAggregationQuery(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->where(function ($i) { return $i % 2 === 0; })
                ->orderByAscending(function ($i) { return -$i; })
                ->groupBy(function ($i) { return $i % 7; })
                ->where(function (\Pinq\ITraversable $i) { return $i->count() % 2 === 0; })
                ->select(function (\Pinq\ITraversable $numbers) {
                    return [
                        'First' => $numbers->first(),
                        'Average' => $numbers->average(),
                        'Count' => $numbers->count(),
                        'Numbers' => $numbers->asArray()
                    ];
                })
                ->indexBy(function (array $values) { return implode(',', $values['Numbers']); });

        $newData =
                array_filter($data, function ($i) {
                    return $i % 2 === 0;
                });
        $newData = array_reverse($newData, true);
        $aggregates = [];

        foreach ($newData as $key => $value) {
            $aggregateKey =
                    array_search($value % 7, array_map(function ($i) {
                        return $i['Key'];
                    }, $aggregates));

            if ($aggregateKey === false) {
                $aggregateKey = count($aggregates) + 1;
                $aggregates[$aggregateKey] = [
                    'Key' => $value % 7,
                    'First' => $value,
                    'Average' => null,
                    'Count' => 0,
                    'Numbers' => []
                ];
            }

            $aggregates[$aggregateKey]['Numbers'][$key] = $value;
            $aggregates[$aggregateKey]['Count']++;
        }

        $indexedAggregates = [];

        foreach ($aggregates as $key => &$value) {
            if ($value['Count'] % 2 !== 0) {
                continue;
            }

            unset($value['Key']);
            $value['Average'] = array_sum($value['Numbers']) / $value['Count'];
            $indexedAggregates[implode(',', $value['Numbers'])] =& $value;
        }

        if ($traversable instanceof IQueryable) {
            $t = 5;
        }
        $this->assertMatches(
                $traversable,
                $indexedAggregates,
                'Complex Aggregate');
    }
}
