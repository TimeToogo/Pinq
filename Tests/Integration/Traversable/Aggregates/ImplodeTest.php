<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ImplodeTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testImplodeEmptyReturnsEmptyString(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertSame('', $Traversable->Implode(','));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testImplodeOperatesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(implode(',', $Data), $Traversable->Implode(','));
    }
}
