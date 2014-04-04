<?php

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    public function testThatTraversableReturnsEquivalentArrayAsSupplied()
    {
        $Data = [1, 2, 'test', 5, 'foo' => 100.01];
        $Numbers = new \Pinq\Traversable($Data);
        
        $this->AssertMatches($Numbers, $Data);
    }
}
