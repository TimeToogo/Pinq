<?php

namespace Pinq\Expressions;

/**
 * Base class for traversing or manipulating an expression tree
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionWalker
{
    /**
     * @return Expression|null
     */
    final public function walk(Expression $expression = null)
    {
        return $expression === null ? null : $expression->traverse($this);
    }

    /**
     * @return Expression[]
     */
    final public function walkAll(array $expressions)
    {
        $walkedExpressions = [];

        foreach ($expressions as $key => $expression) {
            $walkedExpressions[$key] = $this->walk($expression);
        }

        return $walkedExpressions;
    }

    public function walkArray(ArrayExpression $expression)
    {
        return $expression->update(
                $this->walkAll($expression->getItemExpressions()));
    }

    public function walkArrayItem(ArrayItemExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getKeyExpression()),
                $this->walk($expression->getValueExpression()),
                $expression->isReference());
    }

    public function walkAssignment(AssignmentExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getAssignToExpression()),
                $expression->getOperator(),
                $this->walk($expression->getAssignmentValueExpression()));
    }

    public function walkBinaryOperation(BinaryOperationExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getLeftOperandExpression()),
                $expression->getOperator(),
                $this->walk($expression->getRightOperandExpression()));
    }

    public function walkUnaryOperation(UnaryOperationExpression $expression)
    {
        return $expression->update(
                $expression->getOperator(),
                $this->walk($expression->getOperandExpression()));
    }

    public function walkCast(CastExpression $expression)
    {
        return $expression->update(
                $expression->getCastType(),
                $this->walk($expression->getCastValueExpression()));
    }

    public function walkEmpty(EmptyExpression $expression)
    {
        return $expression->update($this->walk($expression->getValueExpression()));
    }

    public function walkIsset(IssetExpression $expression)
    {
        return $expression->update($this->walkAll($expression->getValueExpressions()));
    }

    public function walkField(FieldExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValueExpression()),
                $this->walk($expression->getNameExpression()));
    }

    public function walkMethodCall(MethodCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValueExpression()),
                $this->walk($expression->getNameExpression()),
                $this->walkAll($expression->getArgumentExpressions()));
    }

    public function walkIndex(IndexExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValueExpression()),
                $this->walk($expression->getIndexExpression()));
    }

    public function walkInvocation(InvocationExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValueExpression()),
                $this->walkAll($expression->getArgumentExpressions()));
    }

    public function walkFunctionCall(FunctionCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getNameExpression()),
                $this->walkAll($expression->getArgumentExpressions()));
    }

    public function walkStaticMethodCall(StaticMethodCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClassExpression()),
                $this->walk($expression->getNameExpression()),
                $this->walkAll($expression->getArgumentExpressions()));
    }

    public function walkNew(NewExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClassTypeExpression()),
                $this->walkAll($expression->getArgumentExpressions()));
    }

    public function walkReturn(ReturnExpression $expression)
    {
        return $expression->update($this->walk($expression->getValueExpression()));
    }

    public function walkThrow(ThrowExpression $expression)
    {
        return $expression->update($this->walk($expression->getExceptionExpression()));
    }

    public function walkParameter(ParameterExpression $expression)
    {
        return $expression;
    }

    public function walkTernary(TernaryExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getConditionExpression()),
                $this->walk($expression->getIfTrueExpression()),
                $this->walk($expression->getIfFalseExpression()));
    }

    public function walkVariable(VariableExpression $expression)
    {
        return $expression->update($this->walk($expression->getNameExpression()));
    }

    public function walkValue(ValueExpression $expression)
    {
        return $expression;
    }

    public function walkSubQuery(SubQueryExpression $expression)
    {
        return $expression->updateValue($this->walk($expression->getValueExpression()));
    }

    public function walkClosure(ClosureExpression $expression)
    {
        return $expression->update(
                $this->walkAll($expression->getParameterExpressions()),
                $expression->getUsedVariableNames(),
                $this->walkAll($expression->getBodyExpressions()));
    }
}
