<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class BinaryOperationExpression extends Expression
{
    private $LeftOperandExpression;
    private $Operator;
    private $RightOperandExpression;
    public function __construct(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression)
    {
        $this->LeftOperandExpression = $LeftOperandExpression;
        $this->Operator = $Operator;
        $this->RightOperandExpression = $RightOperandExpression;
    }

    /**
     * @return string The binary operator
     */
    public function GetOperator()
    {
        return $this->Operator;
    }

    /**
     * @return Expression
     */
    public function GetLeftOperandExpression()
    {
        return $this->LeftOperandExpression;
    }

    /**
     * @return Expression
     */
    public function GetRightOperandExpression()
    {
        return $this->RightOperandExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkBinaryOperation($this);
    }

    public function Simplify()
    {
        $Left = $this->LeftOperandExpression->Simplify();
        $Right = $this->RightOperandExpression->Simplify();

        if ($Left instanceof ValueExpression && $Right instanceof ValueExpression) {
            return Expression::Value(self::DoBinaryOperation($Left->GetValue(), $this->Operator, $Right->GetValue()));
        } 
        else if ($Left instanceof ValueExpression || $Right instanceof ValueExpression) {
            $ValueExpression = $Left instanceof ValueExpression ?
                    $Left : $Right;
            $OtherExpression = $Left instanceof ValueExpression ?
                    $Right : $Left;

            $Value = $ValueExpression->GetValue();
            if ($this->Operator === Operators\Binary::LogicalOr && $Value == true) {
                return Expression::Value(true);
            } 
            else if ($this->Operator === Operators\Binary::LogicalOr && $Value == false) {
                return $OtherExpression;
            } 
            else if ($this->Operator === Operators\Binary::LogicalAnd && $Value == false) {
                return Expression::Value(false);
            } 
            else if ($this->Operator === Operators\Binary::LogicalAnd && $Value == true) {
                return $OtherExpression;
            }
        }

        return $this->Update(
                $Left,
                $this->Operator,
                $Right);
    }

    private static $BinaryOperations;
    private static function DoBinaryOperation($Left, $Operator, $Right)
    {
        if (self::$BinaryOperations === null) {
            self::$BinaryOperations = [
                Operators\Binary::BitwiseAnd =>             function ($L, $R) { return $L & $R; },
                Operators\Binary::BitwiseOr =>              function ($L, $R) { return $L | $R; },
                Operators\Binary::BitwiseXor =>             function ($L, $R) { return $L ^ $R; },
                Operators\Binary::ShiftLeft =>              function ($L, $R) { return $L << $R; },
                Operators\Binary::ShiftRight =>             function ($L, $R) { return $L >> $R; },
                Operators\Binary::LogicalAnd =>             function ($L, $R) { return $L && $R; },
                Operators\Binary::LogicalOr =>              function ($L, $R) { return $L || $R; },
                Operators\Binary::Addition =>               function ($L, $R) { return $L + $R; },
                Operators\Binary::Subtraction =>            function ($L, $R) { return $L - $R; },
                Operators\Binary::Multiplication =>         function ($L, $R) { return $L * $R; },
                Operators\Binary::Division =>               function ($L, $R) { return $L / $R; },
                Operators\Binary::Modulus =>                function ($L, $R) { return $L % $R; },
                Operators\Binary::Concatenation =>          function ($L, $R) { return $L . $R; },
                Operators\Binary::IsInstanceOf =>           function ($L, $R) { return $L instanceof $R; },
                Operators\Binary::Equality =>               function ($L, $R) { return $L == $R; },
                Operators\Binary::Identity =>               function ($L, $R) { return $L === $R; },
                Operators\Binary::Inequality =>             function ($L, $R) { return $L != $R; },
                Operators\Binary::NotIdentical =>           function ($L, $R) { return $L !== $R; },
                Operators\Binary::LessThan =>               function ($L, $R) { return $L < $R; },
                Operators\Binary::LessThanOrEqualTo =>      function ($L, $R) { return $L <= $R; },
                Operators\Binary::GreaterThan =>            function ($L, $R) { return $L > $R; },
                Operators\Binary::GreaterThanOrEqualTo =>   function ($L, $R) { return $L >= $R; },
            ];
        }

        $Operation = self::$BinaryOperations[$Operator];

        return $Operation($Left, $Right);
    }

    /**
     * @return self
     */
    public function Update(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression)
    {
        if ($this->LeftOperandExpression === $LeftOperandExpression
                && $this->Operator === $Operator
                && $this->RightOperandExpression === $RightOperandExpression) {
            return $this;
        }

        return new self($LeftOperandExpression, $Operator, $RightOperandExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= '(';
        $this->LeftOperandExpression->CompileCode($Code);
        $Code .= $this->Operator;
        $this->RightOperandExpression->CompileCode($Code);
        $Code .= ')';
    }

}
