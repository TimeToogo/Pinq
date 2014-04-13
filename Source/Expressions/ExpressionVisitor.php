<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionVisitor extends ExpressionWalker
{
    final public function WalkArray(ArrayExpression $Expression)
    {
        $this->VisitArray($Expression);
        return $Expression;
    }
    protected function VisitArray(ArrayExpression $Expression) {}

    final public function WalkAssignment(AssignmentExpression $Expression)
    {
        $this->VisitAssignment($Expression);
        return $Expression;
    }
    protected function VisitAssignment(AssignmentExpression $Expression) {}

    final public function WalkBinaryOperation(BinaryOperationExpression $Expression)
    {
        $this->VisitBinaryOperation($Expression);
        return $Expression;
    }
    protected function VisitBinaryOperation(BinaryOperationExpression $Expression) {}

    final public function WalkCast(CastExpression $Expression)
    {
        $this->VisitCast($Expression);
        return $Expression;
    }
    protected function VisitCast(CastExpression $Expression) {}

    final public function WalkClosure(ClosureExpression $Expression)
    {
        $this->VisitClosure($Expression);
        return $Expression;
    }
    protected function VisitClosure(ClosureExpression $Expression) {}
    
    final public function WalkParameter(ParameterExpression $Expression)
    {
        $this->VisitParameter($Expression);
        return $Expression;
    }
    protected function VisitParameter(ParameterExpression $Expression) {}

    final public function WalkEmpty(EmptyExpression $Expression)
    {
        $this->VisitEmpty($Expression);
        return $Expression;
    }
    protected function VisitEmpty(EmptyExpression $Expression) {}
    
    final public function WalkIsset(IssetExpression $Expression)
    {
        $this->VisitIsset($Expression);
        return $Expression;
    }
    protected function VisitIsset(IssetExpression $Expression) {}

    final public function WalkField(FieldExpression $Expression)
    {
        $this->VisitField($Expression);
        return $Expression;
    }
    protected function VisitField(FieldExpression $Expression) {}

    final public function WalkFunctionCall(FunctionCallExpression $Expression)
    {
        $this->VisitFunctionCall($Expression);
        return $Expression;
    }
    protected function VisitFunctionCall(FunctionCallExpression $Expression) {}

    final public function WalkIndex(IndexExpression $Expression)
    {
        $this->VisitIndex($Expression);
        return $Expression;
    }
    protected function VisitIndex(IndexExpression $Expression) {}

    final public function WalkInvocation(InvocationExpression $Expression)
    {
        $this->VisitInvocation($Expression);
        return $Expression;
    }
    protected function VisitInvocation(InvocationExpression $Expression) {}

    final public function WalkMethodCall(MethodCallExpression $Expression)
    {
        $this->VisitMethodCall($Expression);
        return $Expression;
    }
    protected function VisitMethodCall(MethodCallExpression $Expression) {}

    final public function WalkNew(NewExpression $Expression)
    {
        $this->VisitNew($Expression);
        return $Expression;
    }
    protected function VisitNew(NewExpression $Expression) {}

    final public function WalkReturn(ReturnExpression $Expression)
    {
        $this->VisitReturn($Expression);
        return $Expression;
    }
    protected function VisitReturn(ReturnExpression $Expression) {}

    public function WalkThrow(ThrowExpression $Expression)
    {
        $this->VisitThrow($Expression);
        return $Expression;
    }
    protected function VisitThrow(ThrowExpression $Expression) {}
    
    final public function WalkStaticMethodCall(StaticMethodCallExpression $Expression)
    {
        $this->VisitStaticMethodCall($Expression);
        return $Expression;
    }
    protected function VisitStaticMethodCall(StaticMethodCallExpression $Expression) {}

    final public function WalkSubQuery(SubQueryExpression $Expression)
    {
        $this->VisitSubQuery($Expression);
        return $Expression;
    }
    protected function VisitSubQuery(SubQueryExpression $Expression) {}

    final public function WalkTernary(TernaryExpression $Expression)
    {
        $this->VisitTernary($Expression);
        return $Expression;
    }
    protected function VisitTernary(TernaryExpression $Expression) {}

    final public function WalkUnaryOperation(UnaryOperationExpression $Expression)
    {
        $this->VisitUnaryOperation($Expression);
        return $Expression;
    }
    protected function VisitUnaryOperation(UnaryOperationExpression $Expression) {}

    final public function WalkValue(ValueExpression $Expression)
    {
        $this->VisitValue($Expression);
        return $Expression;
    }
    protected function VisitValue(ValueExpression $Expression) {}

    final public function WalkVariable(VariableExpression $Expression)
    {
        $this->VisitVariable($Expression);
        return $Expression;
    }
    protected function VisitVariable(VariableExpression $Expression) {}

}
