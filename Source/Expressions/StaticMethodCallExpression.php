<?php

namespace Pinq\Expressions;

/**
 * <code>
 * Class::Method('foo')
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class StaticMethodCallExpression extends Expression
{
    /**
     * @var Expression
     */
    private $classExpression;

    /**
     * @var Expression
     */
    private $nameExpression;

    /**
     * @var Expression[]
     */
    private $argumentExpressions;

    public function __construct(Expression $classExpression, Expression $nameExpression, array $argumentExpressions = [])
    {
        $this->classExpression = $classExpression;
        $this->nameExpression = $nameExpression;
        $this->argumentExpressions = $argumentExpressions;
    }

    /**
     * @return Expression
     */
    public function getClassExpression()
    {
        return $this->classExpression;
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
        return $walker->walkStaticMethodCall($this);
    }

    public function simplify()
    {
        return $this->update(
                $this->classExpression->simplify(),
                $this->nameExpression->simplify(),
                self::simplifyAll($this->argumentExpressions));
    }

    /**
     * @return self
     */
    public function update(Expression $classExpression, Expression $nameExpression, array $argumentExpressions = [])
    {
        if ($this->classExpression === $classExpression && $this->nameExpression === $nameExpression && $this->argumentExpressions === $argumentExpressions) {
            return $this;
        }

        return new self($classExpression, $nameExpression, $argumentExpressions);
    }

    protected function compileCode(&$code)
    {
        if ($this->classExpression instanceof ValueExpression) {
            $code .= $this->classExpression->getValue();
        } else {
            $this->classExpression->compileCode($code);
        }

        $code .= '::';

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
        return serialize([$this->classExpression, $this->nameExpression, $this->argumentExpressions]);
    }

    public function unserialize($serialized)
    {
        list($this->classExpression, $this->nameExpression, $this->argumentExpressions) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->classExpression = clone $this->classExpression;
        $this->nameExpression = clone $this->nameExpression;
        $this->argumentExpressions = self::cloneAll($this->argumentExpressions);
    }
}
