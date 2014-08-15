<?php

namespace Pinq\Tests\Integration\Traversable;

class WhereTest extends TraversableTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'where']);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'where'], $data);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatWhereTrueDoesNotFilterAnyData(\Pinq\ITraversable $numbers, array $data)
    {
        $allNumbers = $numbers->where(
                function () {
                    return true;
                }
        );

        $this->assertMatches($allNumbers, $data);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatWhereFalseFiltersAllItems(\Pinq\ITraversable $numbers, array $data)
    {
        $noNumbers = $numbers->where(
                function () {
                    return false;
                }
        );

        $this->assertMatches($noNumbers, []);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatElementsAreFilteredFromTraversableAndPreserveKeys(\Pinq\ITraversable $numbers, array $data)
    {
        $predicate =
                function ($i) {
                    return $i % 2 === 0;
                };
        $evenNumbers = $numbers->where($predicate);

        foreach ($data as $key => $value) {
            if (!$predicate($value)) {
                unset($data[$key]);
            }
        }

        $this->assertMatches($evenNumbers, $data);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatWhereMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range(0, 30, 2));

        $traversable
                ->append($data)
                ->where(
                        function ($i) {
                            return $i % 3 === 0;
                        }
                )
                ->iterate(
                        function (&$i) {
                            $i .= '?';
                        }
                );

        $this->assertSame(['0?', 2, 4, '6?', 8, 10, '12?', 14, 16, '18?', 20, 22, '24?', 26, 28, '30?'], $data);
    }
}
