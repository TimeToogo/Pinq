<?php

namespace Pinq\Tests\Integration\Traversable;

class KeysTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->keys();
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->where($function)->keys();
        });
    }

    /**
     * @dataProvider everything
     */
    public function testThatKeysReturnsTheKeysOfTheValues(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertMatches($traversable->keys(), array_keys($data));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatKeysSupportNonScalarKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $keys = $traversable
                ->indexBy(function () { return new \stdClass(); })
                ->keys();

        $expectedKeys = empty($data) ? [] : array_fill_keys(range(0, count($data) - 1), new \stdClass());

        $this->assertEquals($expectedKeys, $keys->asArray());
    }
}
