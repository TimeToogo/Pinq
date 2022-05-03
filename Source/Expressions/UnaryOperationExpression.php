<?php

namespace Pinq\Expressions;

/**
 * <code>
 * -$I;
 * $I++;
 * </code>
 * @author Elliot Levin <elliotlevin@hotmail.com>
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
    private $operand;

    public function __construct($operator, Expression $operand)
    {
        $this->operator = $operator;
        $this->operand  = $operand;
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
    public function getOperand()
    {
        return $this->operand;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkUnaryOperation($this);
    }

    /**
     * @param int        $operator
     * @param Expression $operand
     *
     * @return self
     */
    public function update($operator, Expression $operand)
    {
        if ($this->operator === $operator && $this->operand === $operand) {
            return $this;
        }

        return new self($operator, $operand);
    }

    protected function compileCode(&$code)
    {
        $code .= '(';
        $code .= sprintf($this->operator, $this->operand->compile());
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->operand, $this->operator]);
    }

    public function __serialize(): array
    {
        return [$this->operand, $this->operator];
    }
    
    public function unserialize($serialized)
    {
        list($this->operand, $this->operator) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->operand, $this->operator) = $data;
    }
    
    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
