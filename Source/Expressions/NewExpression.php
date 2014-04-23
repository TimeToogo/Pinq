<?php

namespace Pinq\Expressions;

/**
 * <code>
 * new \stdClass()
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NewExpression extends Expression
{
    /**
     * @var Expression
     */
    private $classTypeExpression;

    /**
     * @var Expression[]
     */
    private $argumentExpressions;

    public function __construct(Expression $classTypeExpression, array $argumentExpressions = [])
    {
        $this->classTypeExpression = $classTypeExpression;
        $this->argumentExpressions = $argumentExpressions;
    }

    /**
     * @return Expression
     */
    public function getClassTypeExpression()
    {
        return $this->classTypeExpression;
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
        return $walker->walkNew($this);
    }

    public function simplify()
    {
        //TODO: white list of deterministic classes to instanstiate
        return $this->update(
                $this->classTypeExpression->simplify(),
                self::simplifyAll($this->argumentExpressions));
    }

    /**
     * @return self
     */
    public function update(Expression $classTypeExpression, array $argumentExpressions = [])
    {
        if ($this->classTypeExpression === $classTypeExpression && $this->argumentExpressions === $argumentExpressions) {
            return $this;
        }

        return new self($classTypeExpression, $argumentExpressions);
    }

    protected function compileCode(&$code)
    {
        $code .= 'new ';

        if ($this->classTypeExpression instanceof ValueExpression) {
            $code .= $this->classTypeExpression->getValue();
        } else {
            $this->classTypeExpression->compileCode($code);
        }

        $code .= '(';
        $code .= implode(',', self::compileAll($this->argumentExpressions));
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->classTypeExpression, $this->argumentExpressions]);
    }

    public function unserialize($serialized)
    {
        list($this->classTypeExpression, $this->argumentExpressions) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->classTypeExpression = clone $this->classTypeExpression;
        $this->argumentExpressions = self::cloneAll($this->argumentExpressions);
    }
}
