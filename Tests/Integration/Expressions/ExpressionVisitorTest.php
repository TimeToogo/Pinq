<?php

namespace Pinq\Tests\Integration\Expressions;

use \Pinq\Expressions as O;

class ExpressionVisitorTest extends ExpressionTest
{
    public function ExpressionsToVisit()
    {
        return [
            [O\Expression::ArrayExpression([], []), 'VisitArray'],
            [O\Expression::Assign(O\Expression::Value(0), O\Operators\Assignment::Equal, O\Expression::Value(0)), 'VisitAssignment'],
            [O\Expression::BinaryOperation(O\Expression::Value(0), O\Operators\Binary::Addition, O\Expression::Value(0)), 'VisitBinaryOperation'],
            [O\Expression::UnaryOperation(O\Operators\Unary::Plus, O\Expression::Value(0)), 'VisitUnaryOperation'],
            [O\Expression::Cast(O\Operators\Cast::String, O\Expression::Value(0)), 'VisitCast'],
            [O\Expression::Closure([], [], []), 'VisitClosure'],
            [O\Expression::EmptyExpression(O\Expression::Value(0)), 'VisitEmpty'],
            [O\Expression::Field(O\Expression::Value(0), O\Expression::Value(0)), 'VisitField'],
            [O\Expression::FunctionCall(O\Expression::Value(0)), 'VisitFunctionCall'],
            [O\Expression::Index(O\Expression::Value(0), O\Expression::Value(0)), 'VisitIndex'],
            [O\Expression::Invocation(O\Expression::Value(0)), 'VisitInvocation'],
            [O\Expression::IssetExpression([O\Expression::Value(0)]), 'VisitIsset'],
            [O\Expression::MethodCall(O\Expression::Value(0), O\Expression::Value(0)), 'VisitMethodCall'],
            [O\Expression::NewExpression(O\Expression::Value(0)), 'VisitNew'],
            [O\Expression::Parameter(''), 'VisitParameter'],
            [O\Expression::ReturnExpression(), 'VisitReturn'],
            [O\Expression::StaticMethodCall(O\Expression::Value(0), O\Expression::Value(0)), 'VisitStaticMethodCall'],
            [O\Expression::SubQuery(O\Expression::Value(0), $this->getMock('\Pinq\Queries\IRequestQuery'), O\Expression::Invocation(O\Expression::Value(0))), 'VisitSubQuery'],
            [O\Expression::Ternary(O\Expression::Value(0), null, O\Expression::Value(0)), 'VisitTernary'],
            [O\Expression::ThrowExpression(O\Expression::Value(0)), 'VisitThrow'],
            [O\Expression::Value(0), 'VisitValue'],
            [O\Expression::Variable(O\Expression::Value(0)), 'VisitVariable'],
        ];
    }
    
    /**
     * @dataProvider ExpressionsToVisit
     * @covers \Pinq\Expressions\ExpressionVisitor
     */
    public function testExpressionVisitorVisitsTheCorrectMethod(O\Expression $Expression, $Method)
    {
        $ExpressionVisitorMock = $this->getMock('\Pinq\Expressions\ExpressionVisitor', [$Method]);
        
        $ExpressionVisitorMock
                ->expects($this->once())
                ->method($Method)
                ->with($this->equalTo($Expression));
        
        $ExpressionVisitorMock->Walk($Expression);
    }
}