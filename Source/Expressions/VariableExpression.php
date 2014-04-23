<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableExpression extends Expression
{
    /**
     * @var Expression
     */
    private $nameExpression;

    public function __construct(Expression $nameExpression)
    {
        $this->nameExpression = $nameExpression;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkVariable($this);
    }

    public function simplify()
    {
        return $this->update($this->nameExpression->simplify());
    }

    /**
     * @return Expression
     */
    public function getNameExpression()
    {
        return $this->nameExpression;
    }

    public function update(Expression $nameExpression)
    {
        if ($this->nameExpression === $nameExpression) {
            return $this;
        }

        return new self($nameExpression);
    }

    protected function compileCode(&$code)
    {
        if ($this->nameExpression instanceof ValueExpression && \Pinq\Utilities::isNormalSyntaxName($this->nameExpression->getValue())) {
            $code .= '$' . $this->nameExpression->getValue();
        } else {
            $code .= '${';
            $this->nameExpression->compileCode($code);
            $code .= '}';
        }
    }

    public function serialize()
    {
        return serialize($this->nameExpression);
    }

    public function unserialize($serialized)
    {
        $this->nameExpression = unserialize($serialized);
    }

    public function __clone()
    {
        $this->nameExpression = clone $this->nameExpression;
    }
}
