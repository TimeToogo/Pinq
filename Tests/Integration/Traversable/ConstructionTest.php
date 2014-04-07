<?php

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatTraversableReturnsEquivalentArrayAsSupplied(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->AssertMatches($Traversable, $Data);
    }
}
