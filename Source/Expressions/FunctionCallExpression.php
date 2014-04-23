<?php

namespace Pinq\Expressions;

/**
 * <code>
 * strlen($I)
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCallExpression extends Expression
{
    /**
     * @var Expression
     */
    private $nameExpression;

    /**
     * @var Expression[]
     */
    private $argumentExpressions;

    public function __construct(Expression $nameExpression, array $argumentExpressions = [])
    {
        $this->nameExpression = $nameExpression;
        $this->argumentExpressions = $argumentExpressions;
    }

    /**
     * @return Expression
     */
    public function getNameExpression()
    {
        return $this->nameExpression;
    }

    /**
     * @return Expression[]
     */
    public function getArgumentExpressions()
    {
        return $this->argumentExpressions;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkFunctionCall($this);
    }

    public function simplify()
    {
        //TODO: Add a whitelist of deteministic and side-effect free functions.
        return $this->update(
                $this->nameExpression->simplify(),
                self::simplifyAll($this->argumentExpressions));
    }

    /**
     * @return self
     */
    public function update(Expression $nameExpression, array $argumentExpressions = [])
    {
        if ($this->nameExpression === $nameExpression && $this->argumentExpressions === $argumentExpressions) {
            return $this;
        }

        return new self($nameExpression, $argumentExpressions);
    }

    protected function compileCode(&$code)
    {
        if ($this->nameExpression instanceof ValueExpression) {
            $code .= $this->nameExpression->getValue();
        } else {
            $this->nameExpression->compileCode($code);
        }

        $code .= '(';
        $code .= implode(',', self::compileAll($this->argumentExpressions));
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->nameExpression, $this->argumentExpressions]);
    }

    public function unserialize($serialized)
    {
        list($this->nameExpression, $this->argumentExpressions) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->nameExpression = clone $this->nameExpression;
        $this->argumentExpressions = self::cloneAll($this->argumentExpressions);
    }
}
