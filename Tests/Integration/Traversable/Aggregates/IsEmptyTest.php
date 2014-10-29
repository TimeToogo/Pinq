<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class IsEmptyTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIsEmptyReturnsWhetherItHasNoElements(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame($traversable->isEmpty(), empty($data));
    }
}
