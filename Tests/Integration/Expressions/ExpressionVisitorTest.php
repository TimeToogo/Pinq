<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExpressionVisitorTest extends ExpressionTest
{
    /**
     * @dataProvider expressions
     * @covers \Pinq\Expressions\ExpressionVisitor
     */
    public function testExpressionVisitorVisitsTheCorrectMethod(O\Expression $expression)
    {
        $method = 'visit' . $expression->getExpressionTypeName();
        $expressionVisitorMock = $this->createPartialMock('\\Pinq\\Expressions\\ExpressionVisitor', [$method]);

        $expressionVisitorMock
                ->expects($this->once())
                ->method($method)
                ->with($this->equalTo($expression));

        $expressionVisitorMock->walk($expression);
    }
}
