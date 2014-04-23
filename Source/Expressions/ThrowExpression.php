<?php

namespace Pinq\Expressions;

/**
 * <code>
 * throw $I
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ThrowExpression extends Expression
{
    /**
     * @var Expression
     */
    private $exceptionExpression;

    public function __construct(Expression $exceptionExpression)
    {
        $this->exceptionExpression = $exceptionExpression;
    }

    /**
     * @return Expression
     */
    public function getExceptionExpression()
    {
        return $this->exceptionExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkThrow($this);
    }

    public function simplify()
    {
        return $this->update($this->exceptionExpression->simplify());
    }

    /**
     * @return self
     */
    public function update(Expression $exceptionExpression)
    {
        if ($this->exceptionExpression === $exceptionExpression) {
            return $this;
        }

        return new self($exceptionExpression);
    }

    protected function compileCode(&$code)
    {
        $code .= 'throw ';
        $this->exceptionExpression->compileCode($code);
    }

    public function serialize()
    {
        return serialize($this->exceptionExpression);
    }

    public function unserialize($serialized)
    {
        $this->exceptionExpression = unserialize($serialized);
    }

    public function __clone()
    {
        $this->exceptionExpression = clone $this->exceptionExpression;
    }
}
