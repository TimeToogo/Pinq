<?php

namespace Pinq\Tests\Integration\Traversable;

class OrderByTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->orderByAscending(function () {

        });
    }

    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'orderByAscending']);
        $this->assertThatExecutionIsDeferred([$traversable, 'orderByDescending']);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatMultipleExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->orderByAscending($function)->thenByAscending($function)->thenByDescending($function);
        });
    }

    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatOrderByNegatingNumbersIsEquivalentToArrayReverse(\Pinq\ITraversable $numbers, array $data)
    {
        $reversedNumbers = $numbers->orderByAscending(function ($i) { return -$i; });

        $this->assertMatches($reversedNumbers, array_reverse($data, true));
    }

    /**
     * @dataProvider AssocOneToTen
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
     * @dataProvider Names
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
     * @dataProvider Names
     */
    public function testThatOrderStringsCharsAndLengthCharsOrdersCorrectly(\Pinq\ITraversable $names, array $data)
    {
        $orderedNames = $names
                ->orderByAscending(function ($i) { return $i[0]; })
                ->thenByAscending('strlen');

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
     * @dataProvider Names
     */
    public function testThatOrderStringsCharsAndLengthCharsDescendingOrdersCorrectly(\Pinq\ITraversable $names, array $data)
    {
        $orderedNames = $names
                ->orderByDescending(function ($i) { return $i[0]; })
                ->thenByDescending('strlen');

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
     * @dataProvider Names
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
     * @dataProvider Names
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
     * @dataProvider Names
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
     * @dataProvider Names
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
}
