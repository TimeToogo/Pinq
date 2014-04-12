<?php

namespace Pinq;

use \Pinq\Expressions as O;

class FuncBuilder extends FunctionExpressionTree 
{
    private $OriginalFunction;
    
    public function __construct(callable $OriginalFunction, array $ParameterExpressions, array $BodyExpressions)
    {
        parent::__construct($OriginalFunction, $ParameterExpressions, $BodyExpressions);
        $this->OriginalFunction = $OriginalFunction;
    }
    
    private function Unary(\Closure $Function, $Operator)
    {
        return Lambda::Unary(
                $Function, 
                $Operator,
                $this->GetFirstResolvedReturnValueExpression());
    }
    
    private function Binary(\Closure $Function, $Value, $Operator)
    {
        return Lambda::Binary(
                $Function, 
                $this->GetFirstResolvedReturnValueExpression(), 
                $Operator,
                self::ExpressionFor($Value));
    }
    
    // <editor-fold defaultstate="collapsed" desc="Binary">
    
    final public function AndAlso($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) && self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::LogicalAnd);
    }
    
    final public function OrElse($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) || self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::LogicalOr);
    }
    
    final public function IdenticalTo($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) === self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Identity);
    }
    
    final public function NotIdenticalTo($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) !== self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::NotIdentical);
    }
    
    final public function EqualTo($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) == self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Identity);
    }
    
    final public function NotEqualTo($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) != self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Inequality);
    }
    
    final public function Add($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) + self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Addition);
    }
    
    final public function Sub($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) - self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Subtraction);
    }
    
    final public function Mul($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) * self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Multiplication);
    }
    
    final public function Div($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) / self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Division);
    }
    
    final public function Mod($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) % self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Modulus);
    }
    
    final public static function Concat($Value)
    {
        $L = $this->OriginalFunction;
        
        return $this->Binary(
                function ($I) use ($L, $Value) { return $L($I) . self::ValueFor($Value, $I); }, 
                $Value, 
                O\Operators\Binary::Concatenation);
    }
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Unary">
    
    final public function Negate()
    {
        $O = $this->OriginalFunction;
        
        return $this->Unary(
                function ($I) use ($O) { return -$O($I); }, 
                O\Operators\Unary::Negation);
    }
    
    final public function Not()
    {
        $O = $this->OriginalFunction;
        
        return $this->Unary(
                function ($I) use ($O) { return !$O($I); }, 
                O\Operators\Unary::Not);
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Traversal">
    
    /**
     * @return FuncBuilder
     */
    final public function Field($Value)
    {
        $O = $this->OriginalFunction;
        
        return Lambda::ReturnLambda(
                function ($I) use ($O, $Value) { return $O($I)->{self::ValueFor($Value)}; }, 
                O\Expression::Field($this->GetFirstResolvedReturnValueExpression(), self::ExpressionFor($Value)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public function Method($Name, array $Arguments = [])
    {
        $O = $this->OriginalFunction;
        
        return Lambda::ReturnLambda(
                function ($I) use($O, $Name, $Arguments) { return empty($Arguments) ? 
                        $O($I)->{FuncBuilder::ValueFor($Name, $I)}() : 
                        call_user_func_array([$O($I), self::ValueFor($Name, $I)], self::ValuesFor($Arguments)); }, 
                O\Expression::MethodCall($this->Get(), self::ExpressionFor($Name), self::ExpressionsFor($Arguments)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public function Index($Index)
    {
        $O = $this->OriginalFunction;
        
        return Lambda::ReturnLambda(
                function ($I) use($O, $Index) { return $O($I)[self::ValueFor($Index, $I)]; },
                O\Expression::Index($this->GetFirstResolvedReturnValueExpression(), self::ExpressionFor($Index)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public function Invoke(array $Arguments = [])
    {
        $O = $this->OriginalFunction;
        
        return Lambda::ReturnLambda(
                function ($I) use($O, $Arguments) { $Return = $O($I); return empty($Arguments) ? 
                        $Return() : 
                        call_user_func_array($Return, self::ValuesFor($Arguments)); }, 
                O\Expression::Invocation($this->GetFirstResolvedReturnValueExpression(), self::ExpressionsFor($Arguments)));
    }
    
    // </editor-fold>
    
    public static function ValueFor($Value, $I)
    {
        if($Value instanceof FunctionExpressionTree) 
        {
            return $Value($I);
        }
        
        return $Value;
    }
    
    public static function ValuesFor(array $Values)
    {
        return array_map([__CLASS__, 'ValueFor'], $Values);
    }
    
    public static function ExpressionFor($Value)
    {
        if($Value instanceof FunctionExpressionTree) 
        {
            return $Value->GetFirstResolvedReturnValueExpression();
        }
        
        return O\Expression::Value($Value);
    }
    
    public static function ExpressionsFor(array $Values)
    {
        return array_map([__CLASS__, 'ExpressionFor'], $Values);
    }
}

class It
{    
    private static function I() 
    {
        return O\Expression::Variable(O\Expression::Value(Lambda::ParameterName));
    }
    
    /**
     * @return FuncBuilder
     */
    private static function LambdaBinary(\Closure $Function, $BinaryOperator, $Value) 
    {
        return Lambda::Binary(
                $Function, 
                O\Expression::Variable(O\Expression::Value(Lambda::ParameterName)), 
                $BinaryOperator, 
                FuncBuilder::ExpressionFor($Value));
    }
    
    /**
     * @return FuncBuilder
     */
    private static function LambdaUnary(\Closure $Function, $Operator) 
    {
        return Lambda::Unary(
                $Function, 
                $Operator,
                O\Expression::Variable(O\Expression::Value(Lambda::ParameterName)));
    }
    
    /**
     * @return FuncBuilder
     */
    public static function self() 
    {
        return Lambda::ReturnLambda(
                function ($I) { return $I; }, 
                self::I());
    }
    
    /**
     * @return FuncBuilder
     */
    public static function Value() 
    {
        return Lambda::ReturnLambda(
                function ($I) { return $I; }, 
                self::I());
    }
    
    // <editor-fold defaultstate="collapsed" desc="Binary">
    
    /**
     * @return FuncBuilder
     */
    final public static function IdenticalTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I === FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Identity,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function NotIdenticalTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I !== FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::NotIdentical,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function EqualTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I == FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Equality,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function NotEqualTo($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I != FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Inequality,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Add($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I + FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Addition,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Sub($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I - FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Subtraction,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Mul($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I * FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Multiplication,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Div($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I / FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Division,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Mod($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I % FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Modulus,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Concat($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I . FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::Concatenation,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function AndAlso($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I && FuncBuilder::ValueFor($Value, $I); },
                O\Operators\Binary::LogicalAnd,
                $Value);
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function OrElse($Value)
    {
        return self::LambdaBinary(
                function ($I) use ($Value) { return $I || FuncBuilder::ValueFor($Value, $I); },
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
    
    final public static function BitNot()
    {
        return self::LambdaUnary(
                function ($I) { return ~$I; },
                O\Operators\Unary::Increment);
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
    
    // <editor-fold defaultstate="collapsed" desc="Traversal">
    
    /**
     * @return FuncBuilder
     */
    final public static function Field($Value)
    {
        return Lambda::ReturnLambda(
                function ($I) use($Value) { return $I->{FuncBuilder::ValueFor($Value, $I)}; },
                Expressions\Expression::Field(self::I(), FuncBuilder::ExpressionFor($Value)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Method($Name, array $Arguments = [])
    {
        return Lambda::ReturnLambda(
                function ($I) use($Name, $Arguments) { return empty($Arguments) ? 
                        $I->{FuncBuilder::ValueFor($Name, $I)}() : 
                        call_user_func_array([$I, FuncBuilder::ValueFor($Name, $I)], FuncBuilder::ValuesFor($Arguments)); },
                Expressions\Expression::MethodCall(self::I(), FuncBuilder::ExpressionFor($Name), FuncBuilder::ExpressionsFor($Arguments)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Index($Index)
    {
        return Lambda::ReturnLambda(
                function ($I) use($Index) { return $I[FuncBuilder::ValueFor($Index, $I)]; },
                Expressions\Expression::Index(self::I(), FuncBuilder::ExpressionFor($Index)));
    }
    
    /**
     * @return FuncBuilder
     */
    final public static function Invoke(array $Arguments = [])
    {
        return Lambda::ReturnLambda(
                function ($I) use($Arguments) { return empty($Arguments) ? 
                        $I() : 
                        call_user_func_array($I, FuncBuilder::ValuesFor($Arguments)); },
                Expressions\Expression::Invocation(self::I(), FuncBuilder::ExpressionsFor($Arguments)));
    }
    
    // </editor-fold>
    
}

final class Lambda {
    
    private function __construct() {}
    
    const ParameterName = 'I';
    
    public static function ReturnLambda(\Closure $Function, O\Expression $Expression) {
        return new FuncBuilder(
                $Function,
                [O\Expression::Parameter(self::ParameterName)], 
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