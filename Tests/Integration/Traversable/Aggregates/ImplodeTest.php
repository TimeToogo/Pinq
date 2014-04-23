<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ImplodeTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testImplodeEmptyReturnsEmptyString(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame('', $traversable->implode(','));
    }

    /**
     * @dataProvider Everything
     */
    public function testImplodeOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(implode(',', $data), $traversable->implode(','));
    }
}
