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
     * @dataProvider everything
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
    public function testThatIndexByNullReturnsArrayWithLastElement(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function () { return null; });

        $this->assertMatches(
                $indexedElements,
                empty($data) ? [] : [null => end($data)]);
    }
}
