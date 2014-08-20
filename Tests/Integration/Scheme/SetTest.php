<?php

namespace Pinq\Tests\Integration\Scheme;

use Pinq\Iterators\ISet;

class SetTest extends \Pinq\Tests\PinqTestCase
{
    public function sets()
    {
        $orderedMaps = [];

        foreach (\Pinq\Iterators\SchemeProvider::getAvailableSchemes() as $scheme) {
            $orderedMaps[] = [$scheme->createSet(), $scheme];
        }

        return $orderedMaps;
    }

    /**
     * @dataProvider sets
     */
    public function testThatSetCanAddValue(ISet $set)
    {
        $count = 0;
        foreach ([null, 1, 'abc', [122], new \stdClass()] as $value) {
            $this->assertTrue($set->add($value));
            $this->assertTrue($set->contains($value));
            $count++;
            $this->assertCount($count, $set);
        }
    }

    /**
     * @dataProvider sets
     */
    public function testThatSetCannotAddDuplicateValue(ISet $set)
    {
        $value = 'string';

        $this->assertTrue($set->add($value));
        $this->assertCount(1, $set);

        $this->assertFalse($set->add($value));
        $this->assertCount(1, $set);
    }

    /**
     * @dataProvider sets
     */
    public function testThatClearingTheSetRemovesAllValues(ISet $set)
    {
        $this->assertTrue($set->add(1));
        $this->assertTrue($set->add(2));

        $this->assertCount(2, $set);

        $set->clear();

        $this->assertCount(0, $set);
    }

    /**
     * @dataProvider sets
     */
    public function testThatSetUsesStrictEquality(ISet $set)
    {
        $nonIdenticalPairs = [
            [1, 1.0],
            [1, '1'],
            [false, []],
            [false, 0],
            [true, '1'],
            [new \stdClass(), new \stdClass()],
            [[1], ['1']],
            [[0 => 0], ['00' => 0]],
            [[0 => 1, 1 => 2, 2 => 3], [0 => 3, 1 => 2, 2 => 1]]
        ];

        foreach ($nonIdenticalPairs as $nonIdenticalPair) {
            list($value1, $value2) = $nonIdenticalPair;

            $this->assertTrue($set->add($value1));
            $this->assertCount(1, $set);
            $this->assertTrue($set->add($value2));
            $this->assertCount(2, $set);

            $set->clear();
        }
    }

    /**
     * @dataProvider sets
     */
    public function testThatSetContainsReturnsWhetherElementIsAdded(ISet $set)
    {
        $set->add(0);
        $set->add(1);

        $this->assertFalse($set->contains('0'));
        $this->assertFalse($set->contains('1'));
        $this->assertFalse($set->contains(1.0));

        $this->assertTrue($set->contains(0));
        $this->assertTrue($set->contains(1));
    }

    /**
     * @dataProvider sets
     */
    public function testThatSetCanRemoveAddedElements(ISet $set)
    {
        $set->add(0);
        $set->add(1);

        $this->assertCount(2, $set);

        $set->remove('0');

        $this->assertCount(2, $set);

        $set->remove(0);

        $this->assertCount(1, $set);

        $set->remove(1);

        $this->assertCount(0, $set);
    }
}
