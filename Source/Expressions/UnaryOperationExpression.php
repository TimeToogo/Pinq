<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnaryOperationExpression extends Expression
{
    private $Operator;
    private $OperandExpression;
    public function __construct($Operator, Expression $OperandExpression)
    {
        $this->Operator = $Operator;
        $this->OperandExpression = $OperandExpression;
    }

    /**
     * @return string
     */
    public function GetOperator()
    {
        return $this->Operator;
    }

    /**
     * @return Expression
     */
    public function GetOperandExpression()
    {
        return $this->OperandExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkUnaryOperation($this);
    }

    public function Simplify()
    {
        $OperandExpression = $this->OperandExpression->Simplify();

        if ($OperandExpression instanceof ValueExpression) {
            return Expression::Value(self::DoUnaryOperation($this->Operator, $OperandExpression->GetValue()));
        }

        return $this->Update(
                $this->Operator,
                $OperandExpression);
    }

    private static $UnaryOperations;
    private static function DoUnaryOperation($Operator, $Value)
    {
        if (self::$UnaryOperations === null) {
            self::$UnaryOperations = [
                Operators\Unary::BitwiseNot =>      function ($I) { return ~$I; },
                Operators\Unary::Not =>             function ($I) { return !$I; },
                Operators\Unary::Increment =>       function ($I) { return $I++; },
                Operators\Unary::Decrement =>       function ($I) { return $I--; },
                Operators\Unary::PreIncrement =>    function ($I) { return ++$I; },
                Operators\Unary::PreDecrement =>    function ($I) { return --$I; },
                Operators\Unary::Negation =>        function ($I) { return -$I; },
            ];
        }

        $Operation = self::$UnaryOperations[$Operator];

        return $Operation($Value);
    }

    /**
     * @return self
     */
    public function Update($Operator, Expression $OperandExpression)
    {
        if ($this->Operator === $Operator
                && $this->OperandExpression === $OperandExpression) {
            return $this;
        }

        return new self($Operator, $OperandExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= sprintf($this->Operator, $this->OperandExpression->Compile());
    }
}
