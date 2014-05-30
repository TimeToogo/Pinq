<?php

namespace Pinq\Expressions;

/**
 * <code>
 * 'test' => 4
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayItemExpression extends Expression
{
    /**
     * @var Expression|null
     */
    private $keyExpression;

    /**
     * @var Expression
     */
    private $valueExpression;

    /**
     * @var boolean
     */
    private $isReference;

    public function __construct(Expression $keyExpression = null, Expression $valueExpression, $isReference)
    {
        $this->keyExpression = $keyExpression;
        $this->valueExpression = $valueExpression;
        $this->isReference = $isReference;
    }

    /**
     * @return boolean
     */
    public function hasKeyExpression()
    {
        return $this->keyExpression !== null;
    }

    /**
     * @return Expression|null
     */
    public function getKeyExpression()
    {
        return $this->keyExpression;
    }

    /**
     * @return Expression
     */
    public function getValueExpression()
    {
        return $this->valueExpression;
    }

    /**
     * @return boolean
     */
    public function isReference()
    {
        return $this->isReference;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkArrayItem($this);
    }

    public function simplify()
    {
        $keyExpression = $this->keyExpression ? $this->keyExpression->simplify() : null;
        $valueExpression = $this->valueExpression->simplify();

        return $this->update($keyExpression, $valueExpression, $this->isReference);
    }

    /**
     * @return self
     */
    public function update(Expression $keyExpression = null, Expression $valueExpression, $isReference)
    {
        if ($this->keyExpression === $keyExpression 
            && $this->valueExpression === $valueExpression
            && $this->isReference === $isReference) {
            return $this;
        }

        return new self($keyExpression, $valueExpression, $isReference);
    }

    protected function compileCode(&$code)
    {
        if ($this->keyExpression !== null) {
            $this->keyExpression->compileCode($code);
            $code .= ' => ';
        }
        
        if($this->isReference) {
            $code .= '&';
        }
        
        $this->valueExpression->compileCode($code);
    }

    public function serialize()
    {
        return serialize([$this->keyExpression, $this->valueExpression, $this->isReference]);
    }

    public function unserialize($serialized)
    {
        list($this->keyExpression, $this->valueExpression, $this->isReference) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->keyExpression = $this->keyExpression !== null ? clone $this->keyExpression : null;
        $this->valueExpression = clone $this->valueExpression;
    }
}
