<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ImplodeTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider emptyData
     */
    public function testImplodeEmptyReturnsEmptyString(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame('', $traversable->implode(','));
    }

    /**
     * @dataProvider assocTenRandomStrings
     */
    public function testImplodeOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(implode(',', $data), $traversable->implode(','));
    }
}
