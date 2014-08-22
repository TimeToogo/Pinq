<?php

namespace Pinq\Tests\Integration\Traversable;

class ReindexTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->reindex();
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->where($function)->reindex();
        });
    }

    /**
     * @dataProvider everything
     */
    public function testThatValuesReindexesTheValuesByTheirZeroBasedPosition(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertMatches($traversable->reindex(), array_values($data));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatKeysSupportNonScalarKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $values = $traversable
                ->indexBy(function () { return new \stdClass(); })
                ->reindex();

        $this->assertMatches($values, array_values($data));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatReindexMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('a', 'z'));

        $traversable
                ->append($data)
                ->indexBy(function ($i) { return $i; })
                ->reindex()
                ->iterate(function (&$i) { $i .= ';'; });

        $this->assertSame('a;b;c;d;e;f;g;h;i;j;k;l;m;n;o;p;q;r;s;t;u;v;w;x;y;z;', implode('', $data));
    }
}
