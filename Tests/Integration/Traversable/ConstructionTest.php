<?php

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    public function Data()
    {
        return $this->TraversablesFor([1, 2, 'test', 5, 'foo' => 100.01]);
    }
    
    /**
     * @dataProvider Data
     */
    public function testThatTraversableReturnsEquivalentArrayAsSupplied(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertMatches($Traversable, $Data);
    }
}
