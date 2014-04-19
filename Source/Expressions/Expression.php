<?php

namespace Pinq\Expressions;

/**
 * The base class for object expressions.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Expression implements \Serializable
{
    final public static function GetType()
    {
        return get_called_class();
    }

    /**
     * @return Expression
     */
    abstract public function Traverse(ExpressionWalker $Walker);

    /**
     * @return Expression
     */
    abstract public function Simplify();

    /**
     * @return Expression[]
     */
    final public static function SimplifyAll(array $Expressions)
    {
        $ReducedExpressions = [];
        foreach ($Expressions as $Key => $Expression) {
            $ReducedExpressions[$Key] = $Expression->Simplify();
        }

        return $ReducedExpressions;
    }

    /**
     * @return string
     */
    final public function Compile()
    {
        $Code = '';
        $this->CompileCode($Code);

        return $Code;
    }
    abstract protected function CompileCode(&$Code);
    
    final public function __toString()
    {
        return $this->Compile();
    }
    
    /**
     * @return array
     */
    final public static function CompileAll(array $Expressions)
    {
        return array_map(function (self $Expression) { return $Expression->Compile(); }, $Expressions);
    }

    /**
     * @param string $Type
     * @return boolean
     */
    final protected static function AllOfType(array $Expressions, $Type, $AllowNull = false)
    {
        foreach ($Expressions as $Expression) {
            if ($Expression instanceof $Type || $Expression  === null && $AllowNull) {
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
    final public static function CloneAll(array $Expressions)
    {
        return array_map(function (self $Expression = null) { return $Expression === null ? null : clone $Expression; }, $Expressions);
    }

    // <editor-fold desc="Factory Methods">

    /**
     * @return AssignmentExpression
     */
    final public static function Assign(
            Expression $AssignToValueExpression,
            $AssignmentOperator,
            Expression $AssignmentValueExpression) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }

    /**
     * @return BinaryOperationExpression
     */
    final public static function BinaryOperation(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression)
    {
        return new BinaryOperationExpression($LeftOperandExpression, $Operator, $RightOperandExpression);
    }

    /**
     * @return UnaryOperationExpression
     */
    final public static function UnaryOperation($UnaryOperator, Expression $OperandExpression)
    {
        return new UnaryOperationExpression($UnaryOperator, $OperandExpression);
    }

    /**
     * @return NewExpression
     */
    final public static function NewExpression(Expression $ClassTypeExpression, array $ArgumentValueExpressions = [])
    {
        return new NewExpression($ClassTypeExpression, $ArgumentValueExpressions);
    }

    /**
     * @return MethodCallExpression
     */
    final public static function MethodCall(Expression $ValueExpression, Expression $NameExpression, array $ArgumentValueExpressions = [])
    {
        return new MethodCallExpression($ValueExpression, $NameExpression, $ArgumentValueExpressions);
    }

    /**
     * @return FieldExpression
     */
    final public static function Field(Expression $ValueExpression, Expression $NameExpression)
    {
        return new FieldExpression($ValueExpression, $NameExpression);
    }

    /**
     * @return IndexExpression
     */
    final public static function Index(Expression $ValueExpression, Expression $IndexExpression)
    {
        return new IndexExpression($ValueExpression, $IndexExpression);
    }

    /**
     * @return InvocationExpression
     */
    final public static function Invocation(Expression $ValueExpression, array $ArgumentExpressions = [])
    {
        return new InvocationExpression($ValueExpression, $ArgumentExpressions);
    }

    /**
     * @return CastExpression
     */
    final public static function Cast($CastType, Expression $CastValueExpression)
    {
        return new CastExpression($CastType, $CastValueExpression);
    }

    /**
     * @return EmptyExpression
     */
    final public static function EmptyExpression(Expression $ValueExpression)
    {
        return new EmptyExpression($ValueExpression);
    }

    /**
     * @return IssetExpression
     */
    final public static function IssetExpression(array $ValueExpressions)
    {
        return new IssetExpression($ValueExpressions);
    }

    /**
     * @return FunctionCallExpression
     */
    final public static function FunctionCall(Expression $NameExpression, array $ArgumentValueExpressions = [])
    {
        return new FunctionCallExpression($NameExpression, $ArgumentValueExpressions);
    }

    /**
     * @return StaticMethodCallExpression
     */
    final public static function StaticMethodCall(Expression $ClassExpression, Expression $NameExpression, array $ArgumentValueExpressions = [])
    {
        return new StaticMethodCallExpression($ClassExpression, $NameExpression, $ArgumentValueExpressions);
    }

    /**
     * @return TernaryExpression
     */
    final public static function Ternary(
            Expression $ConditionExpression,
            Expression $IfTrueExpression = null,
            Expression $IfFalseExpression) {
        return new TernaryExpression($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
    }

    /**
     * @return ReturnExpression
     */
    final public static function ReturnExpression(Expression $ValueExpression = null)
    {
        return new ReturnExpression($ValueExpression);
    }

    /**
     * @return ThrowExpression
     */
    final public static function ThrowExpression(Expression $ExceptionExpression)
    {
        return new ThrowExpression($ExceptionExpression);
    }

    /**
     * @param string $Name
     * @return ParameterExpression
     */
    final public static function Parameter($Name, $TypeHint = null, $HasDefaultValue = false, $DefaultValue = null, $IsPassedByReference = false)
    {
        return new ParameterExpression($Name, $TypeHint, $HasDefaultValue, $DefaultValue, $IsPassedByReference);
    }

    /**
     * @return ValueExpression
     */
    final public static function Value($Value)
    {
        return new ValueExpression($Value);
    }

    /**
     * @return VariableExpression
     */
    final public static function Variable(Expression $NameExpression)
    {
        return new VariableExpression($NameExpression);
    }

    /**
     * @return ArrayExpression
     */
    final public static function ArrayExpression(array $KeyExpressions, array $ValueExpressions)
    {
        return new ArrayExpression($KeyExpressions, $ValueExpressions);
    }

    /**
     * @return ClosureExpression
     */
    final public static function Closure(array $ParameterExpressions, array $UsedVariables, array $BodyExpressions)
    {
        return new ClosureExpression($ParameterExpressions, $UsedVariables, $BodyExpressions);
    }

    /**
     * @return SubQueryExpression
     */
    final public static function SubQuery(Expression $ValueExpression, \Pinq\Queries\IRequestQuery $RequestQuery)
    {
        return new SubQueryExpression($ValueExpression, $RequestQuery);
    }

    // </editor-fold>
}
