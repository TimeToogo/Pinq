<?php

namespace Pinq\Expressions;

/**
 * Base class for traversing or manipulating an expression tree
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionWalker
{
    /**
     * Walks the expression tree and returns the updated expression tree.
     *
     * @param Expression $expression
     *
     * @return Expression|null
     */
    final public function walk(Expression $expression = null)
    {
        if ($expression === null) {
            return null;
        }

        return $this->doWalk($expression);
    }

    /**
     * @param Expression $expression
     *
     * @return Expression
     */
    protected function doWalk(Expression $expression)
    {
        return $expression->traverse($this);
    }

    /**
     * @param Expression|null[] $expressions
     *
     * @return Expression[]
     */
    final public function walkAll(array $expressions)
    {
        return $this->doWalkAll($expressions);
    }

    /**
     * @param Expression|null[] $expressions
     *
     * @return Expression[]
     */
    protected function doWalkAll(array $expressions)
    {
        foreach ($expressions as $key => $expression) {
            $expressions[$key] = $this->walk($expression);
        }

        return $expressions;
    }

    public function walkArray(ArrayExpression $expression)
    {
        return $expression->update(
                $this->walkAll($expression->getItems())
        );
    }

    public function walkArrayItem(ArrayItemExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getKey()),
                $this->walk($expression->getValue()),
                $expression->isReference()
        );
    }

    public function walkAssignment(AssignmentExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getAssignTo()),
                $expression->getOperator(),
                $this->walk($expression->getAssignmentValue())
        );
    }

    public function walkBinaryOperation(BinaryOperationExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getLeftOperand()),
                $expression->getOperator(),
                $this->walk($expression->getRightOperand())
        );
    }

    public function walkUnaryOperation(UnaryOperationExpression $expression)
    {
        return $expression->update(
                $expression->getOperator(),
                $this->walk($expression->getOperand())
        );
    }

    public function walkCast(CastExpression $expression)
    {
        return $expression->update(
                $expression->getCastType(),
                $this->walk($expression->getCastValue())
        );
    }

    public function walkConstant(ConstantExpression $expression)
    {
        return $expression;
    }

    public function walkClassConstant(ClassConstantExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClass()),
                $expression->getName()
        );
    }

    public function walkEmpty(EmptyExpression $expression)
    {
        return $expression->update($this->walk($expression->getValue()));
    }

    public function walkIsset(IssetExpression $expression)
    {
        return $expression->update($this->walkAll($expression->getValues()));
    }

    public function walkUnset(UnsetExpression $expression)
    {
        return $expression->update($this->walkAll($expression->getValues()));
    }

    public function walkField(FieldExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValue()),
                $this->walk($expression->getName())
        );
    }

    public function walkMethodCall(MethodCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValue()),
                $this->walk($expression->getName()),
                $this->walkAll($expression->getArguments())
        );
    }

    public function walkIndex(IndexExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValue()),
                $this->walk($expression->getIndex())
        );
    }

    public function walkInvocation(InvocationExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValue()),
                $this->walkAll($expression->getArguments())
        );
    }

    public function walkFunctionCall(FunctionCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getName()),
                $this->walkAll($expression->getArguments())
        );
    }

    public function walkStaticMethodCall(StaticMethodCallExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClass()),
                $this->walk($expression->getName()),
                $this->walkAll($expression->getArguments())
        );
    }

    public function walkStaticField(StaticFieldExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClass()),
                $this->walk($expression->getName())
        );
    }

    public function walkNew(NewExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getClass()),
                $this->walkAll($expression->getArguments())
        );
    }

    public function walkReturn(ReturnExpression $expression)
    {
        return $expression->update($this->walk($expression->getValue()));
    }

    public function walkThrow(ThrowExpression $expression)
    {
        return $expression->update($this->walk($expression->getException()));
    }

    public function walkParameter(ParameterExpression $expression)
    {
        return $expression->update(
                $expression->getName(),
                $expression->getTypeHint(),
                $this->walk($expression->getDefaultValue()),
                $expression->isPassedByReference(),
                $expression->isVariadic()
        );
    }

    public function walkArgument(ArgumentExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getValue()),
                $expression->isUnpacked()
        );
    }

    public function walkTernary(TernaryExpression $expression)
    {
        return $expression->update(
                $this->walk($expression->getCondition()),
                $this->walk($expression->getIfTrue()),
                $this->walk($expression->getIfFalse())
        );
    }

    public function walkVariable(VariableExpression $expression)
    {
        return $expression->update($this->walk($expression->getName()));
    }

    public function walkValue(ValueExpression $expression)
    {
        return $expression;
    }

    public function walkClosure(ClosureExpression $expression)
    {
        return $expression->update(
                $expression->returnsReference(),
                $expression->isStatic(),
                $this->walkAll($expression->getParameters()),
                $this->walkAll($expression->getUsedVariables()),
                $this->walkAll($expression->getBodyExpressions())
        );
    }

    public function walkClosureUsedVariable(ClosureUsedVariableExpression $expression)
    {
        return $expression;
    }
}
