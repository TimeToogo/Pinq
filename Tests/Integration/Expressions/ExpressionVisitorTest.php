<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExpressionVisitorTest extends ExpressionTest
{
    public function expressionsToVisit()
    {
        return [
            [O\Expression::arrayExpression([]), 'visitArray'],
            [O\Expression::arrayItem(null, O\Expression::value(0), false), 'visitArrayItem'],
            [O\Expression::assign(
                    O\Expression::value(0),
                    O\Operators\Assignment::EQUAL,
                    O\Expression::value(0)), 'visitAssignment'],
            [O\Expression::binaryOperation(
                    O\Expression::value(0),
                    O\Operators\Binary::ADDITION,
                    O\Expression::value(0)), 'visitBinaryOperation'],
            [O\Expression::unaryOperation(
                    O\Operators\Unary::PLUS,
                    O\Expression::value(0)), 'visitUnaryOperation'],
            [O\Expression::cast(O\Operators\Cast::STRING, O\Expression::value(0)), 'visitCast'],
                [O\Expression::closure(false, false, [], [], []), 'visitClosure'],
                [O\Expression::closureUsedVariable('var'), 'visitClosureUsedVariable'],
            [O\Expression::emptyExpression(O\Expression::value(0)), 'visitEmpty'],
            [O\Expression::field(O\Expression::value(0), O\Expression::value(0)), 'visitField'],
            [O\Expression::functionCall(O\Expression::value(0)), 'visitFunctionCall'],
            [O\Expression::index(O\Expression::value(0), O\Expression::value(0)), 'visitIndex'],
            [O\Expression::invocation(O\Expression::value(0)), 'visitInvocation'],
            [O\Expression::issetExpression([O\Expression::value(0)]), 'visitIsset'],
            [O\Expression::unsetExpression([O\Expression::value(0)]), 'visitUnset'],
            [O\Expression::methodCall(
                    O\Expression::value(0),
                    O\Expression::value(0)), 'visitMethodCall'],
            [O\Expression::newExpression(O\Expression::value(0)), 'visitNew'],
            [O\Expression::parameter(''), 'visitParameter'],
            [O\Expression::returnExpression(), 'visitReturn'],
            [O\Expression::staticMethodCall(
                    O\Expression::value(0),
                    O\Expression::value(0)), 'visitStaticMethodCall'],
            [O\Expression::staticField(
                    O\Expression::value(0),
                    O\Expression::value(0)), 'visitStaticField'],
            [O\Expression::ternary(
                    O\Expression::value(0),
                    null,
                    O\Expression::value(0)), 'visitTernary'],
            [O\Expression::throwExpression(O\Expression::value(0)), 'visitThrow'],
            [O\Expression::value(0), 'visitValue'],
            [O\Expression::variable(O\Expression::value(0)), 'visitVariable'],
            [O\Expression::constant('foo'), 'visitConstant'],
            [O\Expression::classConstant(O\Expression::value(0), 'foo'), 'visitClassConstant'],
        ];
    }

    /**
     * @dataProvider expressionsToVisit
     * @covers \Pinq\Expressions\ExpressionVisitor
     */
    public function testExpressionVisitorVisitsTheCorrectMethod(O\Expression $expression, $method)
    {
        $expressionVisitorMock = $this->getMock('\\Pinq\\Expressions\\ExpressionVisitor', [$method]);

        $expressionVisitorMock
                ->expects($this->once())
                ->method($method)
                ->with($this->equalTo($expression));
        
        $expressionVisitorMock->walk($expression);
    }
}
