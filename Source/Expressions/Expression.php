<?php

namespace Pinq\Expressions;

use Pinq\PinqException;
use Pinq\Utilities;

/**
 * The base class for object expressions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Expression implements \Serializable
{
    const EXPRESSION_TYPE = __CLASS__;

    /**
     * Gets the class name as a string.
     *
     * @return string
     */
    final public static function getType()
    {
        return get_called_class();
    }

    /**
     * Gets a string representing the name of the expression.
     *
     * For instance \Pinq\Expressions\BinaryOperationExpression::getExpressionTypeName()
     * returns 'BinaryOperation'
     *
     * @return string
     */
    final public static function getExpressionTypeName()
    {
        return substr(get_called_class(), strlen(__NAMESPACE__) + 1, -strlen('Expression'));
    }

    /**
     * @param Expression[]       $expressions
     * @param IEvaluationContext $context
     *
     * @return Expression[]
     */
    final public static function simplifyAll(
            array $expressions,
            IEvaluationContext $context = null
    ) {
        $simplifiedExpressions = [];
        foreach ($expressions as $expression) {
            $simplifiedExpressions[] = $expression->simplify($context);
        }

        return $simplifiedExpressions;
    }

    /**
     * Returns whether the supplied name is considered normal name syntax
     * and can be used plainly in code.
     * Example:
     * 'foo' -> yes: $foo
     * 'foo bar' -> no: ${'foo bar'}
     *
     * @param string $name The field, function, method or variable name
     *
     * @return boolean
     */
    final protected static function isNormalSyntaxName($name)
    {
        return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
    }

    /**
     * Verifies the supplied array only contains expressions
     * of the supplied type.
     *
     * @param mixed[] $expressions
     * @param string  $type
     *
     * @return Expression[]
     * @throws PinqException If the array contains invalid expressions.
     */
    final protected static function verifyAll(array $expressions, $type = __CLASS__)
    {
        foreach ($expressions as $key => $expression) {
            if (!($expression instanceof $type)) {
                throw new PinqException(
                        'Invalid array of expressions: invalid expression of type %s at index %s, expecting %s',
                        Utilities::getTypeOrClass($expression),
                        $key,
                        $type);
            }
        }

        return $expressions;
    }

    /**
     * @param ExpressionWalker $walker
     *
     * @return Expression
     */
    abstract public function traverse(ExpressionWalker $walker);

    /**
     * Creates an expression evaluator for the expression with
     * the supplied context.
     *
     * @param IEvaluationContext|null $context
     *
     * @return IEvaluator
     */
    public function asEvaluator(IEvaluationContext $context = null)
    {
        return CompiledEvaluator::fromExpressions([Expression::returnExpression($this)], $context);
    }

    final protected static function cannotEvaluate()
    {
        return new PinqException(
                'Cannot evaluate expression of type %s',
                get_called_class());
    }

    /**
     * Evaluates the expression tree in the supplied context
     * and returns the resulting value.
     *
     * @param IEvaluationContext|null $context
     *
     * @return mixed
     */
    public function evaluate(IEvaluationContext $context = null)
    {
        return $this->asEvaluator($context)->evaluate();
    }

    /**
     * Simplifies the expression tree in the supplied context.
     * Example:
     * <code>
     * -2 + 4
     * </code>
     * Will become:
     * <code>
     * 2
     * </code>
     *
     * @param IEvaluationContext|null $context
     *
     * @return Expression
     */
    public function simplify(IEvaluationContext $context = null)
    {
        return Expression::value($this->evaluate($context));
    }

    /**
     * Returns a value hash for the expression.
     *
     * @return string
     */
    final public function hash()
    {
        return md5($this->compile());
    }

    final public function __toString()
    {
        return $this->compile();
    }

    abstract public function __clone();

    /**
     * Returns whether the expression is equivalent to the supplied expression.
     *
     * @param Expression $expression
     *
     * @return boolean
     */
    public function equals(Expression $expression)
    {
        return $this == $expression;
    }

    /**
     * Returns a value hash for the supplied expressions.
     *
     * @param Expression[] $expressions
     *
     * @return string
     */
    final public static function hashAll(array $expressions)
    {
        return md5(implode('~', self::compileAll($expressions)));
    }

    /**
     * Compiles into equivalent PHP code
     *
     * @param Expression[] $expressions
     *
     * @return string[]
     */
    final public static function compileAll(array $expressions)
    {
        return array_map(
                function (Expression $expression) {
                    return $expression->compile();
                },
                $expressions
        );
    }

    /**
     * Compiles the expression tree into equivalent PHP code.
     *
     * @return string
     */
    final public function compile()
    {
        $code = '';
        $this->compileCode($code);

        return $code;
    }

    /**
     * Compiles the expression tree into debug code.
     *
     * @return string
     */
    final public function compileDebug()
    {
        return (new DynamicExpressionWalker([
                ValueExpression::getType() =>
                        function (ValueExpression $expression) {
                            $value = $expression->getValue();

                            return !is_scalar($value) && $value !== null ?
                                    Expression::constant(
                                            '{' . Utilities::getTypeOrClass($expression->getValue()) . '}'
                                    ) : $expression;
                        }
        ]))
                ->walk($this)
                ->compile();
    }

    abstract protected function compileCode(&$code);

    /**
     * Returns whether the expressions are all of the supplied type.
     *
     * @param Expression[] $expressions
     * @param string       $type
     * @param boolean      $allowNull
     *
     * @return boolean
     */
    final public static function allOfType(array $expressions, $type, $allowNull = false)
    {
        foreach ($expressions as $expression) {
            if ($expression instanceof $type || $expression === null && $allowNull) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param Expression|null[] $expressions
     *
     * @return Expression|null[]
     */
    final public static function cloneAll(array $expressions)
    {
        return array_map(
                function (Expression $expression = null) {
                    return $expression === null ? null : clone $expression;
                },
                $expressions
        );
    }

    /**
     * @param Expression $assignToValue
     * @param string     $assignmentOperator
     * @param Expression $assignmentValue
     *
     * @return AssignmentExpression
     */
    final public static function assign(Expression $assignToValue, $assignmentOperator, Expression $assignmentValue)
    {
        return new AssignmentExpression(
                $assignToValue,
                $assignmentOperator,
                $assignmentValue);
    }

    /**
     * @param Expression $leftOperand
     * @param string     $operator
     * @param Expression $rightOperand
     *
     * @return BinaryOperationExpression
     */
    final public static function binaryOperation(Expression $leftOperand, $operator, Expression $rightOperand)
    {
        return new BinaryOperationExpression(
                $leftOperand,
                $operator,
                $rightOperand);
    }

    /**
     * @param string     $unaryOperator
     * @param Expression $operand
     *
     * @return UnaryOperationExpression
     */
    final public static function unaryOperation($unaryOperator, Expression $operand)
    {
        return new UnaryOperationExpression($unaryOperator, $operand);
    }

    /**
     * @param Expression           $classType
     * @param ArgumentExpression[] $arguments
     *
     * @return NewExpression
     */
    final public static function newExpression(Expression $classType, array $arguments = [])
    {
        return new NewExpression($classType, $arguments);
    }

    /**
     * @param Expression           $value
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return MethodCallExpression
     */
    final public static function methodCall(Expression $value, Expression $name, array $arguments = [])
    {
        return new MethodCallExpression(
                $value,
                $name,
                $arguments);
    }

    /**
     * @param Expression $value
     * @param Expression $name
     *
     * @return FieldExpression
     */
    final public static function field(Expression $value, Expression $name)
    {
        return new FieldExpression($value, $name);
    }

    /**
     * @param Expression      $value
     * @param Expression|null $index
     *
     * @return IndexExpression
     */
    final public static function index(Expression $value, Expression $index = null)
    {
        return new IndexExpression($value, $index);
    }

    /**
     * @param Expression           $valueExpression
     * @param ArgumentExpression[] $arguments
     *
     * @return InvocationExpression
     */
    final public static function invocation(Expression $valueExpression, array $arguments = [])
    {
        return new InvocationExpression($valueExpression, $arguments);
    }

    /**
     * @param string     $castType
     * @param Expression $castValue
     *
     * @return CastExpression
     */
    final public static function cast($castType, Expression $castValue)
    {
        return new CastExpression($castType, $castValue);
    }

    // <editor-fold desc="Factory Methods">

    /**
     * @param Expression $value
     *
     * @return EmptyExpression
     */
    final public static function emptyExpression(Expression $value)
    {
        return new EmptyExpression($value);
    }

    /**
     * @param Expression[] $values
     *
     * @return IssetExpression
     */
    final public static function issetExpression(array $values)
    {
        return new IssetExpression($values);
    }

    /**
     * @param Expression[] $values
     *
     * @return UnsetExpression
     */
    final public static function unsetExpression(array $values)
    {
        return new UnsetExpression($values);
    }

    /**
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return FunctionCallExpression
     */
    final public static function functionCall(Expression $name, array $arguments = [])
    {
        return new FunctionCallExpression($name, $arguments);
    }

    /**
     * @param Expression           $class
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return StaticMethodCallExpression
     */
    final public static function staticMethodCall(Expression $class, Expression $name, array $arguments = [])
    {
        return new StaticMethodCallExpression(
                $class,
                $name,
                $arguments);
    }

    /**
     * @param Expression $class
     * @param Expression $name
     *
     * @return StaticFieldExpression
     */
    final public static function staticField(Expression $class, Expression $name)
    {
        return new StaticFieldExpression(
                $class,
                $name);
    }

    /**
     * @param Expression $condition
     * @param Expression $ifTrue
     * @param Expression $ifFalse
     *
     * @return TernaryExpression
     */
    final public static function ternary(Expression $condition, Expression $ifTrue = null, Expression $ifFalse)
    {
        return new TernaryExpression(
                $condition,
                $ifTrue,
                $ifFalse);
    }

    /**
     * @param Expression|null $value
     *
     * @return ReturnExpression
     */
    final public static function returnExpression(Expression $value = null)
    {
        return new ReturnExpression($value);
    }

    /**
     * @param Expression $exception
     *
     * @return ThrowExpression
     */
    final public static function throwExpression(Expression $exception)
    {
        return new ThrowExpression($exception);
    }

    /**
     * @param string      $name
     * @param string|null $typeHint
     * @param Expression  $defaultValue
     * @param boolean     $isPassedByReference
     * @param boolean     $isVariadic
     *
     * @return ParameterExpression
     */
    final public static function parameter(
            $name,
            $typeHint = null,
            Expression $defaultValue = null,
            $isPassedByReference = false,
            $isVariadic = false
    ) {
        return new ParameterExpression(
                $name,
                $typeHint,
                $defaultValue,
                $isPassedByReference,
                $isVariadic);
    }

    /**
     * @param Expression $value
     * @param boolean    $isUnpacked
     *
     * @return ArgumentExpression
     */
    final public static function argument(
            Expression $value,
            $isUnpacked = false
    ) {
        return new ArgumentExpression($value, $isUnpacked);
    }

    /**
     * @param mixed $value
     *
     * @return ValueExpression
     */
    final public static function value($value)
    {
        return new ValueExpression($value);
    }

    /**
     * @param string $name
     *
     * @return ConstantExpression
     */
    final public static function constant($name)
    {
        return new ConstantExpression($name);
    }

    /**
     * @param Expression $class
     * @param string     $name
     *
     * @return ClassConstantExpression
     */
    final public static function classConstant(Expression $class, $name)
    {
        return new ClassConstantExpression($class, $name);
    }

    /**
     * @param Expression $name
     *
     * @return VariableExpression
     */
    final public static function variable(Expression $name)
    {
        return new VariableExpression($name);
    }

    /**
     * @param ArrayItemExpression[] $items
     *
     * @return ArrayExpression
     */
    final public static function arrayExpression(array $items)
    {
        return new ArrayExpression($items);
    }

    /**
     * @param Expression $key
     * @param Expression $value
     * @param boolean    $isReference
     *
     * @return ArrayItemExpression
     */
    final public static function arrayItem(Expression $key = null, Expression $value, $isReference = false)
    {
        return new ArrayItemExpression($key, $value, $isReference);
    }

    /**
     * @param boolean                         $returnsReference
     * @param boolean                         $isStatic
     * @param ParameterExpression[]           $parameterExpressions
     * @param ClosureUsedVariableExpression[] $usedVariables
     * @param Expression[]                    $bodyExpressions
     *
     * @return ClosureExpression
     */
    final public static function closure(
            $returnsReference,
            $isStatic,
            array $parameterExpressions,
            array $usedVariables,
            array $bodyExpressions
    ) {
        return new ClosureExpression(
                $returnsReference,
                $isStatic,
                $parameterExpressions,
                $usedVariables,
                $bodyExpressions);
    }

    /**
     * @param string  $name
     * @param boolean $isReference
     *
     * @return ClosureUsedVariableExpression
     */
    final public static function closureUsedVariable($name, $isReference = false)
    {
        return new ClosureUsedVariableExpression($name, $isReference);
    }

    // </editor-fold>
}
