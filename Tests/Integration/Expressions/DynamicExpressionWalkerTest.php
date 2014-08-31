<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExtendedExpression extends O\VariableExpression
{

}

class DynamicExpressionWalkerTest extends ExpressionTest
{
    /**
     * @dataProvider expressions
     */
    public function testExpressionWalkerPassesTheCorrectParameters(O\Expression $expression)
    {
        $called           = false;
        $expressionWalker = new O\DynamicExpressionWalker([
                $expression->getType() =>
                        function ($walkedExpression, $calledWalker) use ($expression, &$called, &$expressionWalker) {
                            $called = true;
                            $this->assertSame($expression, $walkedExpression);
                            $this->assertSame($expressionWalker, $calledWalker);
                        }
        ]);

        $expressionWalker->walk($expression);

        $this->assertTrue($called);
    }

    public function testExpressionWalkerPassesTheCorrectParametersWithExtendedExpression()
    {
        $expression       = new ExtendedExpression(O\Expression::value(0));
        $called           = false;
        $expressionWalker = new O\DynamicExpressionWalker([
                O\VariableExpression::getType() =>
                        function ($walkedExpression, $calledWalker) use ($expression, &$called, &$expressionWalker) {
                            $called = true;
                            $this->assertSame($expression, $walkedExpression);
                            $this->assertSame($expressionWalker, $calledWalker);
                        }
        ]);

        $expressionWalker->walk($expression);

        $this->assertTrue($called);
    }

    public function testExpressionWalkerWorks()
    {
        $expression = O\Expression::variable(O\Expression::value('foo'));
        $expressionWalker = new O\DynamicExpressionWalker([
                O\ValueExpression::getType() =>
                        function (O\ValueExpression $expression) {
                            return O\Expression::value('bar');
                        }
        ]);

        $newExpression = $expressionWalker->walk($expression);

        $this->assertNotEquals($expression, $newExpression);
        $this->assertSame($newExpression->getName()->getValue(), 'bar');
    }
}
