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
     * @dataProvider everything
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
}
