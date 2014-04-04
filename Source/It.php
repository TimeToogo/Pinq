<?php

namespace Pinq;

use \Pinq\Expressions as O;

class FuncBuilder extends FunctionExpressionTree 
{
    private $OriginalFunction;
    
    public function __construct(callable $OriginalFunction, array $ParamterNameTypeHintMap, array $Expressions)
    {
        parent::__construct($OriginalFunction, $ParamterNameTypeHintMap, $Expressions);
        $this->OriginalFunction = $OriginalFunction;
    }
    
    private function Unary(\Closure $Function, $Operator)
    {
        return Lambda::Unary(
                $Function, 
                $Operator,
                $this->GetReturnExpression());
    }
    
    private function Binary(\Closure $Function, FuncBuilder $Func, $Operator)
    {
        return Lambda::Binary(
                $Function, 
                $this->GetReturnExpression(), 
                $Operator,
                $Func->GetReturnExpression());
    }
    
    final public function AndAlso(FuncBuilder $Func)
    {
        $L = $this->OriginalFunction;
        $R = $Func->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $R) { return $L($I) && $R($I); }, 
                $Func, 
                O\Operators\Binary::LogicalAnd);
    }
    
    final public static function OrElse(FuncBuilder $Func)
    {
        $L = $this->OriginalFunction;
        $R = $Func->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $R) { return $L($I) || $R($I); }, 
                $Func, 
                O\Operators\Binary::LogicalOr);
    }
}

class It
{
    private function __construct() 
    {
        
    }
    
    private static function LambdaBinary(\Closure $Function, $BinaryOperator, $Value) 
    {
        return Lambda::Binary(
                $Function, 
                O\Expression::Variable(O\Expression::Value(Lambda::ParameterName)), 
                $BinaryOperator, 
                O\Expression::Value($Value));
    }
    
    private static function LambdaUnary(\Closure $Function, $Operator) 
    {
        return Lambda::Unary(
                $Function, 
                $Operator,
                O\Expression::Variable(O\Expression::Value(Lambda::ParameterName)));
    }
    
    public static function self() 
    {
        return Lambda::ReturnLambda(
                function ($I) { return $I; }, 
                O\Expression::Variable(O\Expression::Value(Lambda::ParameterName)));
    }
    
    // <editor-fold defaultstate="collapsed" desc="Binary">
    
    final public static function IdenticalTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I === $Value; },
                O\Operators\Binary::Identity,
                $Value);
    }
    
    final public static function NotIdenticalTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I !== $Value; },
                O\Operators\Binary::NotIdentical,
                $Value);
    }
    
    final public static function EqualTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I == $Value; },
                O\Operators\Binary::Equality,
                $Value);
    }
    
    final public static function NotEqualTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I != $Value; },
                O\Operators\Binary::Inequality,
                $Value);
    }
    
    final public static function Add($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I + $Value; },
                O\Operators\Binary::Addition,
                $Value);
    }
    
    final public static function Sub($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I - $Value; },
                O\Operators\Binary::Subtraction,
                $Value);
    }
    
    final public static function Mul($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I * $Value; },
                O\Operators\Binary::Multiplication,
                $Value);
    }
    
    final public static function Div($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I / $Value; },
                O\Operators\Binary::Division,
                $Value);
    }
    
    final public static function Mod($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I % $Value; },
                O\Operators\Binary::Modulus,
                $Value);
    }
    
    final public static function Concat($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I . $Value; },
                O\Operators\Binary::Concatenation,
                $Value);
    }
    
    final public static function AndAlso($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I && $Value; },
                O\Operators\Binary::LogicalAnd,
                $Value);
    }
    
    final public static function OrElse($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I || $Value; },
                O\Operators\Binary::LogicalOr,
                $Value);
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Unary">
    
    final public static function Negate()
    {
        return self::LambdaUnary(
                function ($I) { return -$I; },
                O\Operators\Unary::Negation);
    }
    
    final public static function Not()
    {
        return self::LambdaUnary(
                function ($I) { return !$I; },
                O\Operators\Unary::Not);
    }
    
    final public static function Inc()
    {
        return self::LambdaUnary(
                function ($I) { return $I++; },
                O\Operators\Unary::Increment);
    }
    
    final public static function Dec()
    {
        return self::LambdaUnary(
                function ($I) { return $I--; },
                O\Operators\Unary::Decrement);
    }
    
    final public static function PreInc()
    {
        return self::LambdaUnary(
                function ($I) { return ++$I; },
                O\Operators\Unary::PreIncrement);
    }
    
    final public static function PreDec()
    {
        return self::LambdaUnary(
                function ($I) { return --$I; },
                O\Operators\Unary::PreDecrement);
    }
    
    // </editor-fold>
    
}

final class Lambda {
    
    private function __construct() {}
    
    const ParameterName = 'I';
    
    public static function ReturnLambda(\Closure $Function, O\Expression $Expression) {
        return new FuncBuilder(
                $Function,
                [self::ParameterName => null], 
                [O\Expression::ReturnExpression($Expression)]);
    }
    
    public static function Binary(\Closure $Function, O\Expression  $Left, $BinaryOperator, O\Expression  $Right) {
        return self::ReturnLambda(
                $Function,
                O\Expression::BinaryOperation(
                        $Left, 
                        $BinaryOperator, 
                        $Right));
    }
    
    public static function Unary(\Closure $Function, $Operator, O\Expression $Operand) {
        return self::ReturnLambda(
                $Function,
                O\Expression::UnaryOperation($Operator, $Operand));
    }
}