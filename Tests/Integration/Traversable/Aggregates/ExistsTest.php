<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ExistsTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatCountReturnsWhetherItHasAnyElements(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals($Traversable->Exists(), !empty($Data));
    }
}
