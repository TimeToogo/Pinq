<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class ExpressionVisitorTest extends ExpressionTest
{
    public function expressionsToVisit()
    {
        return [
            [O\Expression::arrayExpression([], []), 'VisitArray'],
            [O\Expression::assign(
                    O\Expression::value(0),
                    O\Operators\Assignment::EQUAL,
                    O\Expression::value(0)), 'VisitAssignment'],
            [O\Expression::binaryOperation(
                    O\Expression::value(0),
                    O\Operators\Binary::ADDITION,
                    O\Expression::value(0)), 'VisitBinaryOperation'],
            [O\Expression::unaryOperation(
                    O\Operators\Unary::PLUS,
                    O\Expression::value(0)), 'VisitUnaryOperation'],
            [O\Expression::cast(O\Operators\Cast::STRING, O\Expression::value(0)), 'VisitCast'],
            [O\Expression::closure([], [], []), 'VisitClosure'],
            [O\Expression::emptyExpression(O\Expression::value(0)), 'VisitEmpty'],
            [O\Expression::field(O\Expression::value(0), O\Expression::value(0)), 'VisitField'],
            [O\Expression::functionCall(O\Expression::value(0)), 'VisitFunctionCall'],
            [O\Expression::index(O\Expression::value(0), O\Expression::value(0)), 'VisitIndex'],
            [O\Expression::invocation(O\Expression::value(0)), 'VisitInvocation'],
            [O\Expression::issetExpression([O\Expression::value(0)]), 'VisitIsset'],
            [O\Expression::methodCall(
                    O\Expression::value(0),
                    O\Expression::value(0)), 'VisitMethodCall'],
            [O\Expression::newExpression(O\Expression::value(0)), 'VisitNew'],
            [O\Expression::parameter(''), 'VisitParameter'],
            [O\Expression::returnExpression(), 'VisitReturn'],
            [O\Expression::staticMethodCall(
                    O\Expression::value(0),
                    O\Expression::value(0)), 'VisitStaticMethodCall'],
            [O\Expression::subQuery(
                    O\Expression::value(0),
                    $this->getMock('\\Pinq\\Queries\\IRequestQuery'),
                    O\Expression::invocation(O\Expression::value(0))), 'VisitSubQuery'],
            [O\Expression::ternary(
                    O\Expression::value(0),
                    null,
                    O\Expression::value(0)), 'VisitTernary'],
            [O\Expression::throwExpression(O\Expression::value(0)), 'VisitThrow'],
            [O\Expression::value(0), 'VisitValue'],
            [O\Expression::variable(O\Expression::value(0)), 'VisitVariable']
        ];
    }

    /**
     * @dataProvider ExpressionsToVisit
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
