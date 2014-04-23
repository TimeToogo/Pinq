<?php

namespace Pinq\Tests\Integration\Traversable;

class AppendTest extends TraversableTest
{
    protected function _tesstReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->append([]);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatAppendWithSelfReturnsMergedDataWithReindexedKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $appended = $traversable->append($traversable);

        $this->assertMatches(
                $appended,
                array_merge(array_values($data), array_values($data)));
    }

    /**
     * @dataProvider Everything
     */
    public function testThatAppendtWithEmptyReturnsSameAsTheOriginalButReindexedKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $appendedWithTraversable = $traversable->append(new \Pinq\Traversable());
        $appendedWithArray = $traversable->append([]);
        $appendedWithIterator = $traversable->append(new \ArrayObject([]));

        $this->assertMatches($appendedWithTraversable, array_values($data));
        $this->assertMatches($appendedWithArray, array_values($data));
        $this->assertMatches($appendedWithIterator, array_values($data));
    }
}
