<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->select(function () {
            return [];
        });
    }

    /**
     * @dataProvider everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'select']);
    }
    
    /**
     * @dataProvider everything
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParameters([$traversable, 'select'], $data);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatSelectNumbersMapsCorrectlyAndPreservesKeys(\Pinq\ITraversable $values, array $data)
    {
        $multiply =
                function ($i) {
                    return $i * 10;
                };
        $multipliedValues = $values->select($multiply);

        $this->assertMatches($multipliedValues, array_map($multiply, $data));
    }
}
