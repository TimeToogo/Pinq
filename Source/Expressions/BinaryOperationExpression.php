<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $One + $Two;
 * $Five . 'foo';
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BinaryOperationExpression extends Expression
{
    /**
     * @var Expression
     */
    private $leftOperand;

    /**
     * @var int
     */
    private $operator;

    /**
     * @var Expression
     */
    private $rightOperand;

    public function __construct(Expression $leftOperand, $operator, Expression $rightOperand)
    {
        $this->leftOperand  = $leftOperand;
        $this->operator     = $operator;
        $this->rightOperand = $rightOperand;
    }

    /**
     * @return string The binary operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return Expression
     */
    public function getLeftOperand()
    {
        return $this->leftOperand;
    }

    /**
     * @return Expression
     */
    public function getRightOperand()
    {
        return $this->rightOperand;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkBinaryOperation($this);
    }

    /**
     * @param Expression $leftOperand
     * @param int        $operator
     * @param Expression $rightOperand
     *
     * @return self
     */
    public function update(Expression $leftOperand, $operator, Expression $rightOperand)
    {
        if ($this->leftOperand === $leftOperand
                && $this->operator === $operator
                && $this->rightOperand === $rightOperand
        ) {
            return $this;
        }

        return new self($leftOperand, $operator, $rightOperand);
    }

    protected function compileCode(&$code)
    {
        $code .= '(';
        $this->leftOperand->compileCode($code);
        $code .= ' ' . $this->operator . ' ';

        if ($this->operator === Operators\Binary::IS_INSTANCE_OF
                && $this->rightOperand instanceof ValueExpression
        ) {
            $code .= $this->rightOperand->getValue();
        } else {
            $this->rightOperand->compileCode($code);
        }

        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->leftOperand, $this->operator, $this->rightOperand]);
    }

    public function __serialize(): array
    {
        return [$this->leftOperand, $this->operator, $this->rightOperand];
    }
    
    public function unserialize($serialized)
    {
        list($this->leftOperand, $this->operator, $this->rightOperand) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->leftOperand, $this->operator, $this->rightOperand) = $data;
    }
    
    public function __clone()
    {
        $this->leftOperand  = clone $this->leftOperand;
        $this->rightOperand = clone $this->rightOperand;
    }
}
