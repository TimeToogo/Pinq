<?php

namespace Pinq\Expressions;

/**
 * The base class for object expressions.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Expression implements \Serializable
{
    final public static function getType()
    {
        return get_called_class();
    }

    /**
     * @return Expression
     */
    abstract public function traverse(ExpressionWalker $walker);

    /**
     * @return Expression
     */
    abstract public function simplify();

    /**
     * @return Expression[]
     */
    final public static function simplifyAll(array $expressions)
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
    final public function compile()
    {
        $code = '';
        $this->compileCode($code);

        return $code;
    }

    abstract protected function compileCode(&$code);

    final public function __toString()
    {
        return $this->compile();
    }

    /**
     * @return string[]
     */
    final public static function compileAll(array $expressions)
    {
        return array_map(function (self $expression) {
            return $expression->compile();
        }, $expressions);
    }

    /**
     * @param string $type
     * @return boolean
     */
    final protected static function allOfType(array $expressions, $type, $allowNull = false)
    {
        foreach ($expressions as $expression) {
            if ($expression instanceof $type || $expression === null && $allowNull) {
                continue;
            }

            return false;
        }

        return true;
    }

    abstract public function __clone();

    /**
     * @return array
     */
    final public static function cloneAll(array $expressions)
    {
        return array_map(function (self $expression = null) {
            return $expression === null ? null : clone $expression;
        }, $expressions);
    }

    /**
     * Returns whether the supplied name is cosidered normal name syntax
     * and can be used plainly in code.
     *
     * Example:
     * 'foo' -> yes: $foo
     * 'foo bar' -> no: ${'foo bar'}
     *
     * @param string $name The field, function, method or variable name
     * @return boolean
     */
    protected static function isNormalSyntaxName($name)
    {
        return (bool)preg_match('/[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*/', $name);
    }

    // <editor-fold desc="Factory Methods">
    
    /**
     * @return AssignmentExpression
     */
    final public static function assign(Expression $assignToValueExpression, $assignmentOperator, Expression $assignmentValueExpression)
    {
        return new AssignmentExpression(
                $assignToValueExpression,
                $assignmentOperator,
                $assignmentValueExpression);
    }

    /**
     * @return BinaryOperationExpression
     */
    final public static function binaryOperation(Expression $leftOperandExpression, $operator, Expression $rightOperandExpression)
    {
        return new BinaryOperationExpression(
                $leftOperandExpression,
                $operator,
                $rightOperandExpression);
    }

    /**
     * @return UnaryOperationExpression
     */
    final public static function unaryOperation($unaryOperator, Expression $operandExpression)
    {
        return new UnaryOperationExpression($unaryOperator, $operandExpression);
    }

    /**
     * @return NewExpression
     */
    final public static function newExpression(Expression $classTypeExpression, array $argumentValueExpressions = [])
    {
        return new NewExpression($classTypeExpression, $argumentValueExpressions);
    }

    /**
     * @return MethodCallExpression
     */
    final public static function methodCall(Expression $valueExpression, Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new MethodCallExpression(
                $valueExpression,
                $nameExpression,
                $argumentValueExpressions);
    }

    /**
     * @return FieldExpression
     */
    final public static function field(Expression $valueExpression, Expression $nameExpression)
    {
        return new FieldExpression($valueExpression, $nameExpression);
    }

    /**
     * @return IndexExpression
     */
    final public static function index(Expression $valueExpression, Expression $indexExpression)
    {
        return new IndexExpression($valueExpression, $indexExpression);
    }

    /**
     * @return InvocationExpression
     */
    final public static function invocation(Expression $valueExpression, array $argumentExpressions = [])
    {
        return new InvocationExpression($valueExpression, $argumentExpressions);
    }

    /**
     * @return CastExpression
     */
    final public static function cast($castType, Expression $castValueExpression)
    {
        return new CastExpression($castType, $castValueExpression);
    }

    /**
     * @return EmptyExpression
     */
    final public static function emptyExpression(Expression $valueExpression)
    {
        return new EmptyExpression($valueExpression);
    }

    /**
     * @return IssetExpression
     */
    final public static function issetExpression(array $valueExpressions)
    {
        return new IssetExpression($valueExpressions);
    }

    /**
     * @return FunctionCallExpression
     */
    final public static function functionCall(Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new FunctionCallExpression($nameExpression, $argumentValueExpressions);
    }

    /**
     * @return StaticMethodCallExpression
     */
    final public static function staticMethodCall(Expression $classExpression, Expression $nameExpression, array $argumentValueExpressions = [])
    {
        return new StaticMethodCallExpression(
                $classExpression,
                $nameExpression,
                $argumentValueExpressions);
    }

    /**
     * @return TernaryExpression
     */
    final public static function ternary(Expression $conditionExpression, Expression $ifTrueExpression = null, Expression $ifFalseExpression)
    {
        return new TernaryExpression(
                $conditionExpression,
                $ifTrueExpression,
                $ifFalseExpression);
    }

    /**
     * @return ReturnExpression
     */
    final public static function returnExpression(Expression $valueExpression = null)
    {
        return new ReturnExpression($valueExpression);
    }

    /**
     * @return ThrowExpression
     */
    final public static function throwExpression(Expression $exceptionExpression)
    {
        return new ThrowExpression($exceptionExpression);
    }

    /**
     * @param string $name
     * @return ParameterExpression
     */
    final public static function parameter($name, $typeHint = null, $hasDefaultValue = false, $defaultValue = null, $isPassedByReference = false)
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
    final public static function value($value)
    {
        return new ValueExpression($value);
    }

    /**
     * @return VariableExpression
     */
    final public static function variable(Expression $nameExpression)
    {
        return new VariableExpression($nameExpression);
    }

    /**
     * @return ArrayExpression
     */
    final public static function arrayExpression(array $itemExpressions)
    {
        return new ArrayExpression($itemExpressions);
    }
    
    /**
     * @return ArrayItemExpression
     */
    final public static function arrayItem(Expression $keyExpression = null, Expression $valueExpression, $isReference)
    {
        return new ArrayItemExpression($keyExpression, $valueExpression, $isReference);
    }

    /**
     * @return ClosureExpression
     */
    final public static function closure(array $parameterExpressions, array $usedVariables, array $bodyExpressions)
    {
        return new ClosureExpression(
                $parameterExpressions,
                $usedVariables,
                $bodyExpressions);
    }

    /**
     * @return SubQueryExpression
     */
    final public static function subQuery(Expression $valueExpression, \Pinq\Queries\IRequestQuery $requestQuery, TraversalExpression $originalExpression)
    {
        return new SubQueryExpression(
                $valueExpression,                 $requestQuery,                 $originalExpression);
    }

    // </editor-fold>
}
