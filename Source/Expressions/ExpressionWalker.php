<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionWalker
{
    /**
     * @return Expression|null
     */
    final public function Walk(Expression $Expression = null)
    {
        return $Expression === null ? null : $Expression->Traverse($this);
    }

    /**
     * @return Expression[]
     */
    final public function WalkAll(array $Expressions)
    {
        $WalkedExpressions = [];
        foreach ($Expressions as $Key => $Expression) {
            $WalkedExpressions[$Key] = $this->Walk($Expression);
        }

        return $WalkedExpressions;
    }

    public function WalkArray(ArrayExpression $Expression)
    {
        return $Expression->Update(
                $this->WalkAll($Expression->GetKeyExpressions()),
                $this->WalkAll($Expression->GetValueExpressions()));
    }

    public function WalkAssignment(AssignmentExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetAssignmentValueExpression()),
                $Expression->GetOperator(),
                $this->Walk($Expression->GetAssignToExpression()));
    }

    public function WalkBinaryOperation(BinaryOperationExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetLeftOperandExpression()),
                $Expression->GetOperator(),
                $this->Walk($Expression->GetRightOperandExpression()));
    }

    public function WalkUnaryOperation(UnaryOperationExpression $Expression)
    {
        return $Expression->Update(
                $Expression->GetOperator(),
                $this->Walk($Expression->GetOperandExpression()));
    }

    public function WalkCast(CastExpression $Expression)
    {
        return $Expression->Update(
                $Expression->GetCastType(),
                $this->Walk($Expression->GetCastValueExpression()));
    }

    public function WalkEmpty(EmptyExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()));
    }

    public function WalkField(FieldExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->Walk($Expression->GetNameExpression()));
    }

    public function WalkMethodCall(MethodCallExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->Walk($Expression->GetNameExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }

    public function WalkIndex(IndexExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->Walk($Expression->GetIndexExpression()));
    }

    public function WalkInvocation(InvocationExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }

    public function WalkFunctionCall(FunctionCallExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetNameExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }

    public function WalkStaticMethodCall(StaticMethodCallExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetClassExpression()),
                $this->Walk($Expression->GetNameExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }

    public function WalkNew(NewExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetClassTypeExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }

    public function WalkReturn(ReturnExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()));
    }

    public function WalkTernary(TernaryExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetConditionExpression()),
                $this->Walk($Expression->GetIfTrueExpression()),
                $this->Walk($Expression->GetIfFalseExpression()));
    }

    public function WalkVariable(VariableExpression $Expression)
    {
        return $Expression->Update(
                $this->Walk($Expression->GetNameExpression()));
    }

    public function WalkValue(ValueExpression $Expression)
    {
        return $Expression;
    }

    public function WalkClosure(ClosureExpression $Expression)
    {
        return $Expression->Update(
                $Expression->GetParameterNameTypeHintMap(),
                $Expression->GetUsedVariableNames(),
                $this->WalkAll($Expression->GetBodyExpressions()));
    }
}
