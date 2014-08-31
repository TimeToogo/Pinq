<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExpressionSerializationTest extends ExpressionTest
{
    /**
     * @dataProvider expressions
     */
    public function testSerializeAndUnserializeProducesEquivalentExpression(O\Expression $expression)
    {
        $this->assertEquals($expression, unserialize(serialize($expression)));
    }
}
