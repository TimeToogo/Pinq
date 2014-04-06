<?php

namespace Pinq\Tests\Integration\Traversable;

class SelectTest extends TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatSelectNumbersMapsCorrectlyAndPreservesKeys(\Pinq\ITraversable $Values, array $Data)
    {
        $Multiply = function ($I) { return $I * 10; };
        $MultipliedValues = $Values->Select($Multiply);
        
        $this->AssertMatches($MultipliedValues, array_map($Multiply, $Data));
    }
}
