<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExtendedExpression extends O\VariableExpression
{

}

class DynamicExpressionWalkerTest extends ExpressionTest
{
    public function expressionsToWalk()
    {
        return [
            [O\Expression::arrayExpression([])],
            [O\Expression::arrayItem(null, O\Expression::value(0), false)],
            [O\Expression::assign(
                    O\Expression::value(0),
                    O\Operators\Assignment::EQUAL,
                    O\Expression::value(0))],
            [O\Expression::binaryOperation(
                    O\Expression::value(0),
                    O\Operators\Binary::ADDITION,
                    O\Expression::value(0))],
            [O\Expression::unaryOperation(
                    O\Operators\Unary::PLUS,
                    O\Expression::value(0))],
            [O\Expression::cast(O\Operators\Cast::STRING, O\Expression::value(0))],
            [O\Expression::closure(false, false, [], [], [])],
            [O\Expression::emptyExpression(O\Expression::value(0))],
            [O\Expression::field(O\Expression::value(0), O\Expression::value(0))],
            [O\Expression::functionCall(O\Expression::value(0))],
            [O\Expression::index(O\Expression::value(0), O\Expression::value(0))],
            [O\Expression::invocation(O\Expression::value(0))],
            [O\Expression::issetExpression([O\Expression::value(0)])],
            [O\Expression::unsetExpression([O\Expression::value(0)])],
            [O\Expression::methodCall(
                    O\Expression::value(0),
                    O\Expression::value(0))],
            [O\Expression::newExpression(O\Expression::value(0))],
            [O\Expression::parameter('')],
            [O\Expression::returnExpression()],
            [O\Expression::staticMethodCall(
                    O\Expression::value(0),
                    O\Expression::value(0))],
            [O\Expression::staticField(
                    O\Expression::value(0),
                    O\Expression::value(0))],
            [O\Expression::ternary(
                    O\Expression::value(0),
                    null,
                    O\Expression::value(0))],
            [O\Expression::throwExpression(O\Expression::value(0))],
            [O\Expression::value(0)],
            [O\Expression::variable(O\Expression::value(0))],
            [O\Expression::constant('foo')],
            [O\Expression::classConstant(O\Expression::value(0), 'foo')],
        ];
    }

    /**
     * @dataProvider expressionsToWalk
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
                        function (O\ValueExpression $expression)  {
                            return O\Expression::value('bar');
                        }
        ]);

        $newExpression = $expressionWalker->walk($expression);

        $this->assertNotEquals($expression, $newExpression);
        $this->assertSame($newExpression->getName()->getValue(), 'bar');
    }
}
