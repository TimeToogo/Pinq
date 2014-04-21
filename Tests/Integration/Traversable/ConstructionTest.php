<?php

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return \Pinq\Traversable::From($Traversable);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatTraversableReturnsEquivalentArrayAsSupplied(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertMatches($Traversable, $Data);
    }
}
