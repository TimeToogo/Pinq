<?php

namespace Pinq\Expressions;

/**
 * <code>
 * return true
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReturnExpression extends Expression
{
    /**
     * @var Expression
     */
    private $returnValueExpression;

    public function __construct(Expression $returnValueExpression = null)
    {
        $this->returnValueExpression = $returnValueExpression;
    }

    /**
     * @return boolean
     */
    public function hasValueExpression()
    {
        return $this->returnValueExpression !== null;
    }

    /**
     * @return Expression|null
     */
    public function getValueExpression()
    {
        return $this->returnValueExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkReturn($this);
    }

    public function simplify()
    {
        return $this->update($this->returnValueExpression->simplify());
    }

    /**
     * @return self
     */
    public function update(Expression $returnValueExpression = null)
    {
        if ($this->returnValueExpression === $returnValueExpression) {
            return $this;
        }

        return new self($returnValueExpression);
    }

    protected function compileCode(&$code)
    {
        $code .= 'return ';

        if ($this->returnValueExpression !== null) {
            $this->returnValueExpression->compileCode($code);
        }
    }

    public function serialize()
    {
        return serialize($this->returnValueExpression);
    }

    public function unserialize($serialized)
    {
        $this->returnValueExpression = unserialize($serialized);
    }

    public function __clone()
    {
        $this->returnValueExpression = clone $this->returnValueExpression;
    }
}
