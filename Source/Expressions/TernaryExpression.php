<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I ? 1 : -1
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class TernaryExpression extends Expression
{
    /**
     * @var Expression
     */
    private $conditionExpression;

    /**
     * @var Expression
     */
    private $ifTrueExpression;

    /**
     * @var Expression
     */
    private $ifFalseExpression;

    public function __construct(Expression $conditionExpression, Expression $ifTrueExpression = null, Expression $ifFalseExpression)
    {
        $this->conditionExpression = $conditionExpression;
        $this->ifTrueExpression = $ifTrueExpression;
        $this->ifFalseExpression = $ifFalseExpression;
    }

    /**
     * @return Expression
     */
    public function getConditionExpression()
    {
        return $this->conditionExpression;
    }

    /**
     * @return boolean
     */
    public function hasIfTrueExpression()
    {
        return $this->ifTrueExpression !== null;
    }

    /**
     * @return Expression
     */
    public function getIfTrueExpression()
    {
        return $this->ifTrueExpression;
    }

    /**
     * @return Expression
     */
    public function getIfFalseExpression()
    {
        return $this->ifFalseExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkTernary($this);
    }

    public function simplify()
    {
        $conditionExpression = $this->conditionExpression->simplify();
        $ifTrueExpression = $this->ifTrueExpression->simplify();
        $ifFalseExpression = $this->ifFalseExpression->simplify();

        if ($conditionExpression instanceof ValueExpression) {
            return $conditionExpression->getValue() ? $ifTrueExpression : $ifFalseExpression;
        }

        return $this->update(
                $conditionExpression,
                $ifTrueExpression,
                $ifFalseExpression);
    }

    /**
     * @return self
     */
    public function update(Expression $conditionExpression, Expression $ifTrueExpression, Expression $ifFalseExpression)
    {
        if ($this->conditionExpression === $conditionExpression && $this->ifTrueExpression === $ifTrueExpression && $this->ifFalseExpression === $ifFalseExpression) {
            return $this;
        }

        return new self($conditionExpression, $ifTrueExpression, $ifFalseExpression);
    }

    protected function compileCode(&$code)
    {
        $code .= '(';
        $this->conditionExpression->compileCode($code);
        $code .= ' ? ';

        if ($this->ifTrueExpression !== null) {
            $this->ifTrueExpression->compileCode($code);
        }

        $code .= ' : ';
        $this->ifFalseExpression->compileCode($code);
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->conditionExpression, $this->ifTrueExpression, $this->ifFalseExpression]);
    }

    public function unserialize($serialized)
    {
        list($this->conditionExpression, $this->ifTrueExpression, $this->ifFalseExpression) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->conditionExpression = clone $this->conditionExpression;
        $this->ifTrueExpression = clone $this->ifTrueExpression;
        $this->ifFalseExpression = clone $this->ifFalseExpression;
    }
}
