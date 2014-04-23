<?php

namespace Pinq\Expressions;

/**
 * <code>
 * empty($I)
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EmptyExpression extends Expression
{
    /**
     * @var Expression
     */
    private $valueExpression;

    public function __construct(Expression $valueExpression)
    {
        $this->valueExpression = $valueExpression;
    }

    /**
     * @return Expression
     */
    public function getValueExpression()
    {
        return $this->valueExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkEmpty($this);
    }

    public function simplify()
    {
        $valueExpression = $this->valueExpression->simplify();

        if ($valueExpression instanceof ValueExpression) {
            $value = $valueExpression->getValue();

            return Expression::value(empty($value));
        }

        return $this->update($valueExpression);
    }

    /**
     * @return self
     */
    public function update(Expression $valueExpression)
    {
        if ($this->valueExpression === $valueExpression) {
            return $this;
        }

        return new self($valueExpression);
    }

    protected function compileCode(&$code)
    {
        $code .= 'empty(';
        $this->valueExpression->compileCode($code);
        $code .= ')';
    }

    public function serialize()
    {
        return serialize($this->valueExpression);
    }

    public function unserialize($serialized)
    {
        $this->valueExpression = unserialize($serialized);
    }

    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
    }
}
