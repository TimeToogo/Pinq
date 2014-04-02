<?php

namespace Pinq;

use \Pinq\Expressions as O;


class FuncBuilder extends Parsing\FunctionExpressionTree 
{
    public function __construct(callable $OriginalFunction, array $ParamterNameTypeHintMap, array $Expressions)
    {
        parent::__construct($OriginalFunction, $ParamterNameTypeHintMap, $Expressions);
    }
    
    public static function LambdaReturnTree(Closure $Function, O\Expression $LambdaExpression) {
        return new \Pinq\Parsing\FunctionExpressionTree(
                $Function,
                ['I' => null], 
                [O\Expression::ReturnExpression($LambdaExpression)]);
    }
    
    public static function LambdaBinaryTree(Closure $Function, $BinaryOperator, $Value) {
        return self::LambdaReturnTree(
                $Function,
                O\Expression::BinaryOperation(
                        O\Expression::Variable(O\Expression::Value('I')), 
                        $BinaryOperator, 
                        O\Expression::Value($Value)));
    }
    
    final public function AndAlso(FuncBuilder $Func)
    {
        return self::LambdaBinaryTree(
                function ($I) use ($Func) { return $this->C; }, 
                $Func->GetReturnExpression(), 
                $Value);
    }
    
    final public static function OrElse( $Value)
    {
        return self::LambdaBinaryTree(
                function ($I) use ($Value) { return $I || $Value; },
                O\Operators\Binary::LogicalOr,
                $Value);
    }
}

class It
{
    private function __construct() {}

    public function Test()
    {
        It::Is('Test')->Or(It::GreaterThan(5));
        It::Is('Test')->Or(It::GreaterThan(5));
    }
    
    final public static function Identical($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I === $Value; },
                O\Operators\Binary::Identity,
                $Value);
    }
    
    final public static function NotIdentical($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I !== $Value; },
                O\Operators\Binary::NotIdentical,
                $Value);
    }
    
    final public static function Equals($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I == $Value; },
                O\Operators\Binary::Equality,
                $Value);
    }
    
    final public static function NotEqual($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I != $Value; },
                O\Operators\Binary::Inequality,
                $Value);
    }
    
    final public static function Add($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I + $Value; },
                O\Operators\Binary::Addition,
                $Value);
    }
    
    final public static function Sub($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I - $Value; },
                O\Operators\Binary::Subtraction,
                $Value);
    }
    
    final public static function Mul($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I * $Value; },
                O\Operators\Binary::Multiplication,
                $Value);
    }
    
    final public static function Div($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I / $Value; },
                O\Operators\Binary::Division,
                $Value);
    }
    
    final public static function Mod($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I % $Value; },
                O\Operators\Binary::Modulus,
                $Value);
    }
    
    final public static function Concat($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I . $Value; },
                O\Operators\Binary::Concatenation,
                $Value);
    }
    
    final public static function AndAlso($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I && $Value; },
                O\Operators\Binary::LogicalAnd,
                $Value);
    }
    
    final public static function OrElse($Value)
    {
        return FuncBuilder::LambdaBinaryTree(
                function ($I) use ($Value) { return $I || $Value; },
                O\Operators\Binary::LogicalOr,
                $Value);
    }
}
