<?php 

namespace Pinq\Expressions;

/**
 * Base class for traversing an expression tree, only the top
 * expression will be visisted, the subclass should implement such
 * that is visits all the appropriate expressions
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionVisitor extends ExpressionWalker
{
    public final function walkArray(ArrayExpression $expression)
    {
        $this->visitArray($expression);
        
        return $expression;
    }
    
    protected function visitArray(ArrayExpression $expression)
    {
        
    }
    
    public final function walkAssignment(AssignmentExpression $expression)
    {
        $this->visitAssignment($expression);
        
        return $expression;
    }
    
    protected function visitAssignment(AssignmentExpression $expression)
    {
        
    }
    
    public final function walkBinaryOperation(BinaryOperationExpression $expression)
    {
        $this->visitBinaryOperation($expression);
        
        return $expression;
    }
    
    protected function visitBinaryOperation(BinaryOperationExpression $expression)
    {
        
    }
    
    public final function walkCast(CastExpression $expression)
    {
        $this->visitCast($expression);
        
        return $expression;
    }
    
    protected function visitCast(CastExpression $expression)
    {
        
    }
    
    public final function walkClosure(ClosureExpression $expression)
    {
        $this->visitClosure($expression);
        
        return $expression;
    }
    
    protected function visitClosure(ClosureExpression $expression)
    {
        
    }
    
    public final function walkParameter(ParameterExpression $expression)
    {
        $this->visitParameter($expression);
        
        return $expression;
    }
    
    protected function visitParameter(ParameterExpression $expression)
    {
        
    }
    
    public final function walkEmpty(EmptyExpression $expression)
    {
        $this->visitEmpty($expression);
        
        return $expression;
    }
    
    protected function visitEmpty(EmptyExpression $expression)
    {
        
    }
    
    public final function walkIsset(IssetExpression $expression)
    {
        $this->visitIsset($expression);
        
        return $expression;
    }
    
    protected function visitIsset(IssetExpression $expression)
    {
        
    }
    
    public final function walkField(FieldExpression $expression)
    {
        $this->visitField($expression);
        
        return $expression;
    }
    
    protected function visitField(FieldExpression $expression)
    {
        
    }
    
    public final function walkFunctionCall(FunctionCallExpression $expression)
    {
        $this->visitFunctionCall($expression);
        
        return $expression;
    }
    
    protected function visitFunctionCall(FunctionCallExpression $expression)
    {
        
    }
    
    public final function walkIndex(IndexExpression $expression)
    {
        $this->visitIndex($expression);
        
        return $expression;
    }
    
    protected function visitIndex(IndexExpression $expression)
    {
        
    }
    
    public final function walkInvocation(InvocationExpression $expression)
    {
        $this->visitInvocation($expression);
        
        return $expression;
    }
    
    protected function visitInvocation(InvocationExpression $expression)
    {
        
    }
    
    public final function walkMethodCall(MethodCallExpression $expression)
    {
        $this->visitMethodCall($expression);
        
        return $expression;
    }
    
    protected function visitMethodCall(MethodCallExpression $expression)
    {
        
    }
    
    public final function walkNew(NewExpression $expression)
    {
        $this->visitNew($expression);
        
        return $expression;
    }
    
    protected function visitNew(NewExpression $expression)
    {
        
    }
    
    public final function walkReturn(ReturnExpression $expression)
    {
        $this->visitReturn($expression);
        
        return $expression;
    }
    
    protected function visitReturn(ReturnExpression $expression)
    {
        
    }
    
    public function walkThrow(ThrowExpression $expression)
    {
        $this->visitThrow($expression);
        
        return $expression;
    }
    
    protected function visitThrow(ThrowExpression $expression)
    {
        
    }
    
    public final function walkStaticMethodCall(StaticMethodCallExpression $expression)
    {
        $this->visitStaticMethodCall($expression);
        
        return $expression;
    }
    
    protected function visitStaticMethodCall(StaticMethodCallExpression $expression)
    {
        
    }
    
    public final function walkSubQuery(SubQueryExpression $expression)
    {
        $this->visitSubQuery($expression);
        
        return $expression;
    }
    
    protected function visitSubQuery(SubQueryExpression $expression)
    {
        
    }
    
    public final function walkTernary(TernaryExpression $expression)
    {
        $this->visitTernary($expression);
        
        return $expression;
    }
    
    protected function visitTernary(TernaryExpression $expression)
    {
        
    }
    
    public final function walkUnaryOperation(UnaryOperationExpression $expression)
    {
        $this->visitUnaryOperation($expression);
        
        return $expression;
    }
    
    protected function visitUnaryOperation(UnaryOperationExpression $expression)
    {
        
    }
    
    public final function walkValue(ValueExpression $expression)
    {
        $this->visitValue($expression);
        
        return $expression;
    }
    
    protected function visitValue(ValueExpression $expression)
    {
        
    }
    
    public final function walkVariable(VariableExpression $expression)
    {
        $this->visitVariable($expression);
        
        return $expression;
    }
    
    protected function visitVariable(VariableExpression $expression)
    {
        
    }
}