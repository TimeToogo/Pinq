<?php

namespace Pinq\Expressions;

/**
 * Base class for traversing an expression tree, only the top
 * expression will be visited, the subclass should implement such
 * that is visits all the appropriate expressions
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionVisitor extends ExpressionWalker
{
    final public function walkArray(ArrayExpression $expression)
    {
        $this->visitArray($expression);

        return $expression;
    }

    protected function visitArray(ArrayExpression $expression)
    {

    }

    final public function walkArrayItem(ArrayItemExpression $expression)
    {
        $this->visitArrayItem($expression);

        return $expression;
    }

    protected function visitArrayItem(ArrayItemExpression $expression)
    {

    }

    final public function walkAssignment(AssignmentExpression $expression)
    {
        $this->visitAssignment($expression);

        return $expression;
    }

    protected function visitAssignment(AssignmentExpression $expression)
    {

    }

    final public function walkBinaryOperation(BinaryOperationExpression $expression)
    {
        $this->visitBinaryOperation($expression);

        return $expression;
    }

    protected function visitBinaryOperation(BinaryOperationExpression $expression)
    {

    }

    final public function walkCast(CastExpression $expression)
    {
        $this->visitCast($expression);

        return $expression;
    }

    protected function visitCast(CastExpression $expression)
    {

    }

    final public function walkClosure(ClosureExpression $expression)
    {
        $this->visitClosure($expression);

        return $expression;
    }

    protected function visitClosure(ClosureExpression $expression)
    {

    }

    final public function walkClosureUsedVariable(ClosureUsedVariableExpression $expression)
    {
        $this->visitClosureUsedVariable($expression);

        return $expression;
    }

    protected function visitClosureUsedVariable(ClosureUsedVariableExpression $expression)
    {

    }

    public function walkConstant(ConstantExpression $expression)
    {
        $this->visitConstant($expression);

        return $expression;
    }

    protected function visitConstant(ConstantExpression $expression)
    {

    }

    public function walkClassConstant(ClassConstantExpression $expression)
    {
        $this->visitClassConstant($expression);

        return $expression;
    }

    protected function visitClassConstant(ClassConstantExpression $expression)
    {

    }

    final public function walkParameter(ParameterExpression $expression)
    {
        $this->visitParameter($expression);

        return $expression;
    }

    protected function visitParameter(ParameterExpression $expression)
    {

    }

    final public function walkArgument(ArgumentExpression $expression)
    {
        $this->visitArgument($expression);

        return $expression;
    }

    protected function visitArgument(ArgumentExpression $expression)
    {

    }

    final public function walkEmpty(EmptyExpression $expression)
    {
        $this->visitEmpty($expression);

        return $expression;
    }

    protected function visitEmpty(EmptyExpression $expression)
    {

    }

    final public function walkIsset(IssetExpression $expression)
    {
        $this->visitIsset($expression);

        return $expression;
    }

    protected function visitIsset(IssetExpression $expression)
    {

    }

    final public function walkUnset(UnsetExpression $expression)
    {
        $this->visitUnset($expression);

        return $expression;
    }

    protected function visitUnset(UnsetExpression $expression)
    {

    }

    final public function walkField(FieldExpression $expression)
    {
        $this->visitField($expression);

        return $expression;
    }

    protected function visitField(FieldExpression $expression)
    {

    }

    final public function walkFunctionCall(FunctionCallExpression $expression)
    {
        $this->visitFunctionCall($expression);

        return $expression;
    }

    protected function visitFunctionCall(FunctionCallExpression $expression)
    {

    }

    final public function walkIndex(IndexExpression $expression)
    {
        $this->visitIndex($expression);

        return $expression;
    }

    protected function visitIndex(IndexExpression $expression)
    {

    }

    final public function walkInvocation(InvocationExpression $expression)
    {
        $this->visitInvocation($expression);

        return $expression;
    }

    protected function visitInvocation(InvocationExpression $expression)
    {

    }

    final public function walkMethodCall(MethodCallExpression $expression)
    {
        $this->visitMethodCall($expression);

        return $expression;
    }

    protected function visitMethodCall(MethodCallExpression $expression)
    {

    }

    final public function walkNew(NewExpression $expression)
    {
        $this->visitNew($expression);

        return $expression;
    }

    protected function visitNew(NewExpression $expression)
    {

    }

    final public function walkReturn(ReturnExpression $expression)
    {
        $this->visitReturn($expression);

        return $expression;
    }

    protected function visitReturn(ReturnExpression $expression)
    {

    }

    final public function walkThrow(ThrowExpression $expression)
    {
        $this->visitThrow($expression);

        return $expression;
    }

    protected function visitThrow(ThrowExpression $expression)
    {

    }

    final public function walkStaticMethodCall(StaticMethodCallExpression $expression)
    {
        $this->visitStaticMethodCall($expression);

        return $expression;
    }

    protected function visitStaticMethodCall(StaticMethodCallExpression $expression)
    {

    }

    final public function walkStaticField(StaticFieldExpression $expression)
    {
        $this->visitStaticField($expression);

        return $expression;
    }

    protected function visitStaticField(StaticFieldExpression $expression)
    {

    }

    final public function walkTernary(TernaryExpression $expression)
    {
        $this->visitTernary($expression);

        return $expression;
    }

    protected function visitTernary(TernaryExpression $expression)
    {

    }

    final public function walkUnaryOperation(UnaryOperationExpression $expression)
    {
        $this->visitUnaryOperation($expression);

        return $expression;
    }

    protected function visitUnaryOperation(UnaryOperationExpression $expression)
    {

    }

    final public function walkValue(ValueExpression $expression)
    {
        $this->visitValue($expression);

        return $expression;
    }

    protected function visitValue(ValueExpression $expression)
    {

    }

    final public function walkVariable(VariableExpression $expression)
    {
        $this->visitVariable($expression);

        return $expression;
    }

    protected function visitVariable(VariableExpression $expression)
    {

    }
}
