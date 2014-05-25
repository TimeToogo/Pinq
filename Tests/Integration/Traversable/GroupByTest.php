<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupByTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameType(\Pinq\ITraversable $traversable)
    {
        return $traversable->groupBy(function () {

        });
    }

    /**
     * @dataProvider everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'groupBy']);
    }

    /**
     * @dataProvider everything
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParameters([$traversable, 'groupBy'], $data);
    }

    /**
     * @dataProvider everything
     */
    public function testThatGroupByElementReturnsCorrectTraversableType(\Pinq\ITraversable $traversable, array $data)
    {
        $groups =
                $traversable->groupBy(function ($i) {
                    return $i;
                });

        foreach ($groups as $group) {
            if($traversable instanceof \Pinq\IQueryable) {
                $this->assertInstanceOf(\Pinq\ITraversable::ITRAVERSABLE_TYPE, $group);
            } else if ($traversable instanceof \Pinq\IRepository) {
                $this->assertInstanceOf(\Pinq\IRepository::IREPOSITORY_TYPE, $group);
            } else { 
                $this->assertInstanceOf(get_class($traversable), $group);
            }
        }
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatGroupByMultipleTest(\Pinq\ITraversable $traversable, array $data)
    {
        $groups = $traversable
                ->groupBy(function ($i) { return [$i % 2 === 0, $i % 3 === 0]; })->asArray();

        $this->assertCount(4, $groups);
        $this->assertMatchesValues($groups[0], [1, 5, 7]);
        $this->assertMatchesValues($groups[1], [2, 4, 8, 10]);
        $this->assertMatchesValues($groups[2], [3, 9]);
        $this->assertMatchesValues($groups[3], [6]);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatGroupByGroupsTheElementsCorrectlyAndPreservesKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $isEven =
                function ($i) {
                    return $i % 2 === 0;
                };

        //First number is odd, so the first group should be the odd group
        list($odd, $even) = $traversable->groupBy($isEven)->asArray();

        $this->assertMatches(
                $odd,
                array_filter($data, function ($i) use ($isEven) {
                    return !$isEven($i);
                }));
        $this->assertMatches($even, array_filter($data, $isEven));
    }
}
