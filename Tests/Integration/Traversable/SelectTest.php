<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->select(function () {
            return [];
        });
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'select']);
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testCalledWithValueAndKeyParameters(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder([$traversable, 'select'], $data);
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

    public function padStrings()
    {
        return $this->getImplementations([
            5   => 'abc',
            10  => 'hello',
            20  => 'foo-bar-baz',
        ]);
    }

    /**
     * @dataProvider padStrings
     */
    public function testThatSelectSupportsInternalFunctionsWithOneParameter(\Pinq\ITraversable $values, array $data)
    {
        //Signature: strlen($string)
        $paddedValues = $values->select('strlen');
        //Ensure that even though the key will be the second parameter, it does not trigger an error.

        $this->assertMatches($paddedValues, [
            5   => 3,
            10  => 5,
            20  => 11,
        ]);
    }

    /**
     * @dataProvider padStrings
     */
    public function testThatSelectUsesDefaultToValuesForUnsuppliedArgumentsToInternalFunctions(\Pinq\ITraversable $values, array $data)
    {
        //Signature: str_pad($input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_RIGHT)
        $paddedValues = $values->select('str_pad');
        //So the string should be $input and the key should be $pad_length and the rest is default values.

        $this->assertMatches($paddedValues, [
            5   => 'abc  ',
            10  => 'hello     ',
            20  => 'foo-bar-baz         ',
        ]);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatSelectDoesNotMaintainReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('Z', 'A'));

        $traversable
                ->append($data)
                ->select(function & (&$i) { return $i; })
                ->iterate(function (&$i) { $i .= $i; });

        $this->assertSame(range('Z', 'A'), $data);
    }
}
