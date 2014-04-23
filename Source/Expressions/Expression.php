<?php 

namespace Pinq\Expressions;

/**
 * The base class for object expressions.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Expression implements \Serializable
{
    public static final function getType()
    {
        return get_called_class();
    }
    
    /**
     * @return Expression
     */
    public abstract function traverse(ExpressionWalker $walker);
    
    /**
     * @return Expression
     */
    public abstract function simplify();
    
    /**
     * @return Expression[]
     */
    public static final function simplifyAll(array $expressions)
    {
        $reducedExpressions = [];
        
        foreach ($expressions as $key => $expression) {
            $reducedExpressions[$key] = $expression->simplify();
        }
        
        return $reducedExpressions;
    }
    
    /**
     * Compiles into equivalent PHP code
     * 
     * @return string
     */
    public final function compile()
    {
        $code = '';
        $this->compileCode($code);
        
        return $code;
    }
    
    protected abstract function compileCode(&$code);
    
    public final function __toString()
    {
        return $this->compile();
    }
    
    /**
     * @return string[]
     */
    public static final function compileAll(array $expressions)
    {
        return array_map(function (self $expression) {
            return $expression->compile();
        }, $expressions);
    }
    
    /**
     * @param string $type
     * @return boolean
     */
    protected static final function allOfType(array $expressions, $type, $allowNull = false)
    {
        foreach ($expressions as $expression) {
            if ($expression instanceof $type || $expression === null && $allowNull) {
                continue;
            }
            
            return false;
        }
        
        return true;
    }
    
    public abstract function __clone();
    
    /**
     * @return array
     */
    public static final function cloneAll(array $expressions)
    {
        return array_map(function (self $expression = null) {
            return $expression === null ? null : clone $expression;
        }, $expressions);
    }
    
    // <editor-fold desc="Factory Methods">
    /**
     * @return AssignmentExpression
     */
    public static final function assign(Expression $assignToValueExpression, $assignmentOperator, Expression $assignmentValueExpression)
    {
        return new AssignmentExpression(
                $assignToValueExpression,
                $assignmentOperator,
                $assignmentValueExpression);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    public static final function binaryOperation(Expression $leftOperandExpression, $operator, Expression $rightOperandExpression)
    {
        return new BinaryOperationExpression(
                $leftOperandExpression,
                $operator,
                $rightOperandExpression);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    public static final function unaryOperation($unaryOperator, Expression $operandExpression)
    {
        return new UnaryOperationExpression($unaryOperator, $operandExpression);
    }
    
    /**
     * @return NewExpression
     */
    public static final function newExpression(Expression $classTypeExpression, array $argumentValueExpressions = [])
    {
        return new NewExpression($classTypeExpression, $argumentValueExpressions);
    }
    
    /**
     * @return MethodCallExpression
     */
    public static final function methodCall(Expression $valueExpression, Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new MethodCallExpression(
                $valueExpression,
                $nameExpression,
                $argumentValueExpressions);
    }
    
    /**
     * @return FieldExpression
     */
    public static final function field(Expression $valueExpression, Expression $nameExpression)
    {
        return new FieldExpression($valueExpression, $nameExpression);
    }
    
    /**
     * @return IndexExpression
     */
    public static final function index(Expression $valueExpression, Expression $indexExpression)
    {
        return new IndexExpression($valueExpression, $indexExpression);
    }
    
    /**
     * @return InvocationExpression
     */
    public static final function invocation(Expression $valueExpression, array $argumentExpressions = [])
    {
        return new InvocationExpression($valueExpression, $argumentExpressions);
    }
    
    /**
     * @return CastExpression
     */
    public static final function cast($castType, Expression $castValueExpression)
    {
        return new CastExpression($castType, $castValueExpression);
    }
    
    /**
     * @return EmptyExpression
     */
    public static final function emptyExpression(Expression $valueExpression)
    {
        return new EmptyExpression($valueExpression);
    }
    
    /**
     * @return IssetExpression
     */
    public static final function issetExpression(array $valueExpressions)
    {
        return new IssetExpression($valueExpressions);
    }
    
    /**
     * @return FunctionCallExpression
     */
    public static final function functionCall(Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new FunctionCallExpression($nameExpression, $argumentValueExpressions);
    }
    
    /**
     * @return StaticMethodCallExpression
     */
    public static final function staticMethodCall(Expression $classExpression, Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new StaticMethodCallExpression(
                $classExpression,
                $nameExpression,
                $argumentValueExpressions);
    }
    
    /**
     * @return TernaryExpression
     */
    public static final function ternary(Expression $conditionExpression, Expression $ifTrueExpression = null, Expression $ifFalseExpression)
    {
        return new TernaryExpression(
                $conditionExpression,
                $ifTrueExpression,
                $ifFalseExpression);
    }
    
    /**
     * @return ReturnExpression
     */
    public static final function returnExpression(Expression $valueExpression = null)
    {
        return new ReturnExpression($valueExpression);
    }
    
    /**
     * @return ThrowExpression
     */
    public static final function throwExpression(Expression $exceptionExpression)
    {
        return new ThrowExpression($exceptionExpression);
    }
    
    /**
     * @param string $name
     * @return ParameterExpression
     */
    public static final function parameter($name, $typeHint = null, $hasDefaultValue = false, $defaultValue = null, $isPassedByReference = false)
    {
        return new ParameterExpression(
                $name,
                $typeHint,
                $hasDefaultValue,
                $defaultValue,
                $isPassedByReference);
    }
    
    /**
     * @return ValueExpression
     */
    public static final function value($value)
    {
        return new ValueExpression($value);
    }
    
    /**
     * @return VariableExpression
     */
    public static final function variable(Expression $nameExpression)
    {
        return new VariableExpression($nameExpression);
    }
    
    /**
     * @return ArrayExpression
     */
    public static final function arrayExpression(array $keyExpressions, array $valueExpressions)
    {
        return new ArrayExpression($keyExpressions, $valueExpressions);
    }
    
    /**
     * @return ClosureExpression
     */
    public static final function closure(array $parameterExpressions, array $usedVariables, array $bodyExpressions)
    {
        return new ClosureExpression(
                $parameterExpressions,
                $usedVariables,
                $bodyExpressions);
    }
    
    /**
     * @return SubQueryExpression
     */
    public static final function subQuery(Expression $valueExpression, \Pinq\Queries\IRequestQuery $requestQuery, TraversalExpression $originalExpression)
    {
        return new SubQueryExpression(
                $valueExpression,
                $requestQuery,
                $originalExpression);
    }
}