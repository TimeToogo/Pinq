<?php

namespace Pinq\Tests\Integration\Traversable;

class AppendTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->append([]);
    }

    /**
     * @dataProvider everything
     */
    public function testThatAppendWithSelfReturnsMergedDataWithReindexedKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $appended = $traversable->append($traversable);

        $this->assertMatches(
                $appended,
                array_merge(array_values($data), array_values($data)));
    }

    /**
     * @dataProvider everything
     */
    public function testThatAppendWithEmptyReturnsSameAsTheOriginalButReindexedKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $appendedWithTraversable = $traversable->append([]);
        $appendedWithArray = $traversable->append([]);
        $appendedWithIterator = $traversable->append(new \ArrayObject([]));

        $this->assertMatches($appendedWithTraversable, array_values($data));
        $this->assertMatches($appendedWithArray, array_values($data));
        $this->assertMatches($appendedWithIterator, array_values($data));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatAppendMaintainsReferences(\Pinq\ITraversable $traversable, array $data)
    {
        $ref = 5;
        $traversable = $traversable->append([&$ref]);

        $this->assertReferenceEquals($ref, $traversable->asArray()[0]);
    }
}
