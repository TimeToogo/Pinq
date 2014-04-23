<?php 

namespace Pinq\Tests\Integration\Traversable;

class ConstructionTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return \Pinq\Traversable::from($traversable);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatTraversableReturnsEquivalentArrayAsSupplied(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertMatches($traversable, $data);
    }
}