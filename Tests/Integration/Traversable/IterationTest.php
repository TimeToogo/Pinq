<?php

namespace Pinq\Tests\Integration\Traversable;

class NonIntegerOrStringIterator extends \IteratorIterator
{
    #[\ReturnTypeWillChange]
    public function key()
    {
        return new \stdClass();
    }
}

class IterationTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIndexReturnsCorrectValues(\Pinq\ITraversable $traversable, array $data)
    {
        $iteratedData = [];

        foreach ($traversable as $key => $value) {
            $iteratedData[$key] = $value;
        }

        $this->assertSame($data, $iteratedData);
    }

    /**
     * @dataProvider everything
     */
    public function testIteratePassedAllValuesAndKeysToFunction(\Pinq\ITraversable $traversable, array $data)
    {
        $iteratedData = [];

        $traversable->iterate(function ($value, $key) use (&$iteratedData) {
            $iteratedData[$key] = $value;
        });

        $this->assertSame($data, $iteratedData);
    }

    /**
     * @dataProvider everything
     */
    public function testIterateWillStopAfterReturningFalse(\Pinq\ITraversable $traversable, array $data)
    {
        if ($traversable->count() < 3) {
            return;
        }

        $count = 0;

        $traversable->iterate(function ($value, $key) use (&$count) {
            $count++;

            //Must use strict equality
            if ($count === 1) {
                return '';
            } elseif ($count === 2) {
                return 0;
            } elseif ($count === 3) {
                return false;
            }
        });

        $this->assertSame($count, 3);
    }

    /**
     * @dataProvider everything
     */
    public function testThatNonIntegerAndStringKeysAreReindexed(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ([new \stdClass(), [], [1], fopen('php://input', 'r'), 3.22, null, true] as $notIntegerOrString) {
            $withNonIntOrString = $traversable
                    ->take(1)
                    ->indexBy(function () use ($notIntegerOrString) { return $notIntegerOrString; });

            $this->assertSame(empty($data) ? [] : [0 => reset($data)], $withNonIntOrString->asArray());
        }
    }

    /**
     * @dataProvider everything
     */
    public function testThatIdenticalNonIntegerOrStringMapToTheSameScalarKey(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ([new \stdClass(), [], [1], fopen('php://input', 'r'), 3.22, null, true] as $nonStringOrInt) {
            $withNonIntOrString = $traversable
                    ->indexBy(function () use ($nonStringOrInt) { return $nonStringOrInt; });

            $this->assertSame(empty($data) ? [] : [0 => reset($data)], $withNonIntOrString->asArray());

            if (is_object($nonStringOrInt)) {
                //Cloned object longer identical, should map to individual keys
                $withNonIntOrString = $traversable
                        ->indexBy(function () use ($nonStringOrInt) { return unserialize(serialize($nonStringOrInt)); });

                $this->assertSame(array_values($data), $withNonIntOrString->asArray());
            }
        }
    }

    /**
     * @dataProvider everything
     */
    public function testThatNonIntegerOrStringAreReindexedWhenConvertingToArrayOrIteratingButNotForIterateMethodOrTrueIterator(\Pinq\ITraversable $traversable, array $data)
    {
        $nonIntegerOrString = [
            4 => new \stdClass(),
            5 => [],
            7 => fopen('php://input', 'r'),
        ];

        $traversable = $traversable
                ->take(0)
                ->append(range(1, 9))
                ->indexBy(function ($value) use ($nonIntegerOrString) {
                    return isset($nonIntegerOrString[$value]) ?
                            $nonIntegerOrString[$value] :
                            //Should handle string intergers being auto cast to ints
                            (string) ($value * 2);
                });

        $expectedData = [
            2 => 1,
            4 => 2,
            6 => 3,

            //1 + largest integer key
            7 => 4,

            //1 + largest integer key
            8 => 5,

            12 => 6,

            //1 + largest integer key
            13 => 7,

            16 => 8,
            18 => 9,
        ];

        $this->assertSame($expectedData, $traversable->asArray());

        $iteratedData = [];

        foreach ($traversable as $key => $value) {
            $iteratedData[$key] = $value;
        }

        $this->assertSame($expectedData, $iteratedData);

        $assertCorrectKeyValuePair = function ($value, $key) use ($nonIntegerOrString) {
            if (isset($nonIntegerOrString[$value])) {
                $this->assertSame($nonIntegerOrString[$value], $key);
            } else {
                $this->assertSame((string) ($value * 2), $key);
            }
        };

        $traversable->iterate($assertCorrectKeyValuePair);

        $trueIterator = $traversable->getTrueIterator();
        $trueIterator = $trueIterator instanceof \Iterator ? $trueIterator : new \IteratorIterator($trueIterator);

        $trueIterator->rewind();
        while ($trueIterator->valid()) {
            $assertCorrectKeyValuePair($trueIterator->current(), $trueIterator->key());
            $trueIterator->next();
        }
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatInterationPassesReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('A', 'Z'));

        $traversable
                ->append($data)
                ->iterate(function (&$i) { $i = null; });

        $this->assertSame(array_fill_keys(range(0, 25), null), $data);
    }
}
