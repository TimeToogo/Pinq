<?php

namespace Pinq\Tests\Integration\Traversable;

class IntersectTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameType(\Pinq\ITraversable $traversable)
    {
        return $traversable->intersect([]);
    }

    /**
     * @dataProvider everything
     */
    public function testThatIntersectWithSelfReturnsUniqueValues(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->intersect($traversable);

        $this->assertMatches($intersection, array_unique($data));
    }

    /**
     * @dataProvider everything
     */
    public function testThatIntersectWithEmptyReturnsEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->intersect(new \Pinq\Traversable());

        $this->assertMatches($intersection, []);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatIntersectionWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $intersection = $traversable->intersect($otherData);

        $this->assertMatches(
                $intersection,
                array_intersect($data, $otherData));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatIntersectUsesStrictEquality(\Pinq\ITraversable $traversable, array $data)
    {
        $insection = $traversable->intersect(['1', '2', '3', '4', '5']);

        $this->assertMatches($insection, []);
    }
}
