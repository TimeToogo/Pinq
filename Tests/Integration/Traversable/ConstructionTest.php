<?php

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatTraversableReturnsEquivalentArrayAsSupplied(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertMatches($traversable, $data);
    }
}
