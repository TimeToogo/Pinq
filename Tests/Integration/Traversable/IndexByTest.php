<?php

namespace Pinq\Tests\Integration\Traversable;

class IndexByTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->indexBy(function () {

        });
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'indexBy']);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'indexBy'], $data);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatIndexByElementIndexesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function ($i) { return $i; });

        $this->assertMatches($indexedElements, array_combine($data, $data));
    }

    /**
     * @dataProvider everything
     */
    public function testThatIndexByNullReturnsArrayWithFirstAssociatedValue(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function () { return null; });

        $this->assertMatches(
                $indexedElements,
                empty($data) ? [] : [0 => reset($data)]);
    }

    /**
     * @dataProvider everything
     */
    public function testThatIndexByDuplicateKeyWithForeachOnlyReturnsFirstAssociatedValue(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function () { return null; });

        $first = true;
        foreach ($indexedElements as $key => $element) {
            $this->assertSame($key, 0);
            $this->assertSame(reset($data), $element);
            $this->assertTrue($first);
            $first = false;
        }
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatIndexByMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('A', 'G', 2));

        $traversable
                ->append($data)
                ->indexBy(function ($i) { return $i . '-key'; })
                ->iterate(function (&$i) { $i .= ':'; });

        $this->assertSame(['A:', 'C:', 'E:', 'G:'], $data);
    }
}
