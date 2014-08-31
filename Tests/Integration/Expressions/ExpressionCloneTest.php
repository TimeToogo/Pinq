<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExpressionCloneTest extends ExpressionTest
{
    /**
     * @dataProvider expressions
     */
    public function testCloningProducesEquivalentExpression(O\Expression $expression)
    {
        $this->assertEquals($expression, clone $expression);
    }
}
