<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class StringTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function someStrings()
    {
        return $this->implementationsFor([
            'Foo',
            'Bar',
            'Test',
            'Pinq',
            'Data',
            'Lorem Ipsum',
            'Dallas'
        ]);
    }

    /**
     * @dataProvider someStrings
     */
    public function testOrderingMultiple(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->orderByAscending(function ($i) { return $i[0]; })
                ->thenByDescending(function ($i) { return $i[2]; });

        $this->assertMatches(
                $traversable,
                [
                    1 => 'Bar',
                    4 => 'Data',
                    6 => 'Dallas',
                    0 => 'Foo',
                    5 => 'Lorem Ipsum',
                    3 => 'Pinq',
                    2 => 'Test'
                ]);
    }

    public function pHPDocExample()
    {
        return $this->implementationsFor([
            0 => ['volume' => 67, 'edition' => 2],
            1 => ['volume' => 86, 'edition' => 1],
            2 => ['volume' => 85, 'edition' => 6],
            3 => ['volume' => 98, 'edition' => 2],
            4 => ['volume' => 86, 'edition' => 6],
            5 => ['volume' => 67, 'edition' => 7]
        ]);
    }

    /**
     * @dataProvider PHPDocExample
     */
    public function testOrderingMultiplePHPDocExampleButPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->orderByDescending(function ($i) { return $i['volume']; })
                ->thenByAscending(function ($i) { return $i['edition']; });

        $this->assertMatches(
                $traversable,
                [
                    3 => ['volume' => 98, 'edition' => 2],
                    1 => ['volume' => 86, 'edition' => 1],
                    4 => ['volume' => 86, 'edition' => 6],
                    2 => ['volume' => 85, 'edition' => 6],
                    0 => ['volume' => 67, 'edition' => 2],
                    5 => ['volume' => 67, 'edition' => 7]
                ]);
    }

    /**
     * @dataProvider someStrings
     */
    public function testSelectManyQuery(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($traversable->selectMany(function ($i) { return str_split($i); })->getTrueIterator() as $value) {

        }

        $string = $traversable
                ->selectMany(function ($i) { return str_split($i); })
                ->select(function ($char) { return $char; })
                ->implode('');

        $trueString = '';

        foreach ($data as $i) {
            foreach (str_split($i) as $char) {
                $trueString .= $char;
            }
        }

        $this->assertEquals($trueString, $string);
    }

    /**
     * @dataProvider someStrings
     */
    public function testAggregateValuesString(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(true, $traversable->all(), 'All');

        $this->assertEquals(true, $traversable->any(), 'Any');

        $this->assertEquals(
                array_sum(array_map('strlen', $data)) / count($data),
                $traversable->average('strlen'),
                'Average string length');

        $this->assertEquals(
                array_sum(array_map('strlen', $data)),
                $traversable->sum('strlen'),
                'Sum string length');

        $this->assertEquals(
                array_unique($data),
                $traversable->unique()->asArray(),
                'Unique');

        $this->assertEquals(
                implode('- -- -', $data),
                $traversable->implode('- -- -'),
                'String implode');
    }
}
