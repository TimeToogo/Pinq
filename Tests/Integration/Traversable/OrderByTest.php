<?php

namespace Pinq\Tests\Integration\Traversable;

class OrderByTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->orderByAscending(function () {

        });
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'orderByAscending']);
        $this->assertThatExecutionIsDeferred([$traversable, 'orderByDescending']);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'orderByAscending'], $data);
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'orderByDescending'], $data);
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable->orderByAscending(function ($i) { return $i; }), 'thenByAscending'], $data);
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable->orderByAscending(function ($i) { return $i; }), 'thenByDescending'], $data);
    }

    /**
     * @dataProvider everything
     */
    public function testThatMultipleExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->orderByAscending($function)->thenByAscending($function)->thenByDescending($function);
        });
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatOrderByNegatingNumbersIsEquivalentToArrayReverse(\Pinq\ITraversable $numbers, array $data)
    {
        $reversedNumbers = $numbers->orderByAscending(function ($i) { return -$i; });

        $this->assertMatches($reversedNumbers, array_reverse($data, true));
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatDescendingNegatingNumbersIsEquivalentToOriginal(\Pinq\ITraversable $numbers, array $data)
    {
        $unalteredNumbers = $numbers->orderByDescending(function ($i) { return -$i; });

        $this->assertMatches($unalteredNumbers, $data);
    }

    public function names()
    {
        return $this->getImplementations([
            'Fred',
            'Sam',
            'Daniel',
            'Frank',
            'Andrew',
            'Taylor',
            'Sandy'
        ]);
    }

    /**
     * @dataProvider names
     */
    public function testThatOrderStringsByMultipleCharsOrdersCorrectly(\Pinq\ITraversable $names, array $data)
    {
        $orderedNames = $names
                ->orderByAscending(function ($i) { return $i[0]; })
                ->thenByAscending(function ($i) { return $i[2]; });

        $this->assertMatchesValues(
                $orderedNames,
                [
                    'Andrew',
                    'Daniel',
                    'Frank',
                    'Fred',
                    'Sam',
                    'Sandy',
                    'Taylor'
                ]);
    }

    /**
     * @dataProvider names
     */
    public function testThatOrderStringsCharsAndLengthCharsOrdersCorrectly(\Pinq\ITraversable $names, array $data)
    {
        $orderedNames = $names
                ->orderByAscending(function ($i) { return $i[0]; })
                ->thenByAscending(function ($i) { return strlen($i); });

        $this->assertMatchesValues(
                $orderedNames,
                [
                    'Andrew',
                    'Daniel',
                    'Fred',
                    'Frank',
                    'Sam',
                    'Sandy',
                    'Taylor'
                ]);
    }

    /**
     * @dataProvider names
     */
    public function testThatOrderStringsCharsAndLengthCharsDescendingOrdersCorrectly(\Pinq\ITraversable $names, array $data)
    {
        $orderedNames = $names
                ->orderByDescending(function ($i) { return $i[0]; })
                ->thenByDescending(function ($i) { return strlen($i); });

        $this->assertMatchesValues(
                $orderedNames,
                [
                    'Taylor',
                    'Sandy',
                    'Sam',
                    'Frank',
                    'Fred',
                    'Daniel',
                    'Andrew'
                ]);
    }

    /**
     * @dataProvider names
     */
    public function testThatOrderByAscendingIsEquivalentToOrderByWithAscendingDirection(\Pinq\ITraversable $names, array $data)
    {
        $function =
                function ($i) {
                    return $i[0];
                };

        $orderedNames = $names->orderByAscending($function);
        $otherOrderedNames = $names->orderBy($function, \Pinq\Direction::ASCENDING);

        $this->assertSame(
                $orderedNames->asArray(),
                $otherOrderedNames->asArray());
    }

    /**
     * @dataProvider names
     */
    public function testThatOrderByDescendingIsEquivalentToOrderByWithDescendingDirection(\Pinq\ITraversable $names, array $data)
    {
        $function =
                function ($i) {
                    return $i[0];
                };

        $orderedNames = $names->orderByDescending($function);
        $otherOrderedNames = $names->orderBy($function, \Pinq\Direction::DESCENDING);

        $this->assertSame(
                $orderedNames->asArray(),
                $otherOrderedNames->asArray());
    }

    /**
     * @dataProvider names
     */
    public function testThatThenByAscendingIsEquivalentToThenByWithAscendingDirection(\Pinq\ITraversable $names, array $data)
    {
        $irrelaventOrderByFunction =
                function ($i) {
                    return 1;
                };
        $thenFunction =
                function ($i) {
                    return $i[2];
                };

        $orderedNames = $names
                ->orderByAscending($irrelaventOrderByFunction)
                ->thenByAscending($thenFunction);
        $otherOrderedNames = $names
                ->orderByAscending($irrelaventOrderByFunction)
                ->thenBy($thenFunction, \Pinq\Direction::ASCENDING);

        $this->assertSame(
                $orderedNames->asArray(),
                $otherOrderedNames->asArray());
    }

    /**
     * @dataProvider names
     */
    public function testThatThenByDescendingIsEquivalentToThenByWithDescendingDirection(\Pinq\ITraversable $names, array $data)
    {
        $irrelaventOrderByFunction =
                function ($i) {
                    return 1;
                };
        $thenFunction =
                function ($i) {
                    return $i[2];
                };

        $orderedNames = $names
                ->orderByAscending($irrelaventOrderByFunction)
                ->thenByDescending($thenFunction);

        $otherOrderedNames = $names
                ->orderByAscending($irrelaventOrderByFunction)
                ->thenBy($thenFunction, \Pinq\Direction::DESCENDING);

        $this->assertSame(
                $orderedNames->asArray(),
                $otherOrderedNames->asArray());
    }

    public function dates()
    {
        return $this->getImplementations([
            new \DateTime('2000-1-1'),
            new \DateTime('2001-1-1'),
            new \DateTime('2002-1-1'),
            new \DateTime('2003-1-1'),
            new \DateTime('2004-1-1'),
            new \DateTime('2005-1-1'),
        ]);
    }

    /**
     * @dataProvider dates
     */
    public function testThatOrderByOrdersDatesCorrectly(\Pinq\ITraversable $dates, array $data)
    {
        $years = $dates
                ->orderByAscending(function (\DateTime $date) { return $date; })
                ->select(function (\DateTime $date) { return $date->format('Y'); })
                ->implode(':');

        $this->assertSame('2000:2001:2002:2003:2004:2005', $years);
    }

    /**
     * @dataProvider dates
     */
    public function testThatOrderByMaintainsNonScalarKeys(\Pinq\ITraversable $dates, array $data)
    {
        $years = $dates
                ->indexBy(function (\DateTime $date) { return $date; })
                ->select(function (\DateTime $date) { return (int) $date->format('Y'); })
                ->orderByDescending(function ($year, \DateTime $date) { return $year; })
                ->select(function ($year, \DateTime $date) { return $date->format('Y'); })
                ->implode(':');

        $this->assertSame('2005:2004:2003:2002:2001:2000', $years);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatOrderByMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range(1, 100));

        $traversable
                ->append($data)
                ->orderByAscending(function ($i) { return (int) ($i / 10); })
                ->thenByDescending(function ($i) { return $i; })
                ->iterate(function (&$i) { $i *= 10; });

        $this->assertSame(range(10, 1000, 10), $data);
    }
}
