<?php

namespace Pinq\Expressions;

/**
 * <code>
 * -$I;
 * $I++;
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnaryOperationExpression extends Expression
{
    /**
     * @var int
     */
    private $operator;

    /**
     * @var Expression
     */
    private $operandExpression;

    public function __construct($operator, Expression $operandExpression)
    {
        $this->operator = $operator;
        $this->operandExpression = $operandExpression;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return Expression
     */
    public function getOperandExpression()
    {
        return $this->operandExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkUnaryOperation($this);
    }

    public function simplify()
    {
        $operandExpression = $this->operandExpression->simplify();

        if ($operandExpression instanceof ValueExpression) {
            return Expression::value(self::doUnaryOperation(
                    $this->operator,
                    $operandExpression->getValue()));
        }

        return $this->update($this->operator, $operandExpression);
    }

    private static $unaryOperations;

    private static function doUnaryOperation($operator, $value)
    {
        if (self::$unaryOperations === null) {
            self::$unaryOperations = [
                Operators\Unary::BITWISE_NOT =>     function ($i) { return ~$i; },
                Operators\Unary::NOT =>             function ($i) { return !$i; },
                Operators\Unary::INCREMENT =>       function ($i) { return $i++; },
                Operators\Unary::DECREMENT =>       function ($i) { return $i--; },
                Operators\Unary::PRE_INCREMENT =>   function ($i) { return ++$i; },
                Operators\Unary::PRE_DECREMENT =>   function ($i) { return --$i; },
                Operators\Unary::NEGATION =>        function ($i) { return -$i; },
                Operators\Unary::PLUS =>            function ($i) { return +$i; }
            ];
        }

        $operation = self::$unaryOperations[$operator];

        return $operation($value);
    }

    /**
     * @return self
     */
    public function update($operator, Expression $operandExpression)
    {
        if ($this->operator === $operator && $this->operandExpression === $operandExpression) {
            return $this;
        }

        return new self($operator, $operandExpression);
    }

    protected function compileCode(&$code)
    {
        $code .= '(';
        $code .= sprintf($this->operator, $this->operandExpression->compile());
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->operandExpression, $this->operator]);
    }

    public function unserialize($serialized)
    {
        list($this->operandExpression, $this->operator) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->operandExpression = clone $this->operandExpression;
    }
}
