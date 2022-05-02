<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $condition ? 1 : -1
 * </code>
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TernaryExpression extends Expression
{
    /**
     * @var Expression
     */
    private $condition;

    /**
     * @var Expression|null
     */
    private $ifTrue;

    /**
     * @var Expression
     */
    private $ifFalse;

    public function __construct(Expression $condition, Expression $ifTrue = null, Expression $ifFalse)
    {
        $this->condition = $condition;
        $this->ifTrue    = $ifTrue;
        $this->ifFalse   = $ifFalse;
    }

    /**
     * @return Expression
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return boolean
     */
    public function hasIfTrue()
    {
        return $this->ifTrue !== null;
    }

    /**
     * @return Expression
     */
    public function getIfTrue()
    {
        return $this->ifTrue;
    }

    /**
     * @return Expression
     */
    public function getIfFalse()
    {
        return $this->ifFalse;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkTernary($this);
    }

    /**
     * @param Expression      $condition
     * @param Expression|null $ifTrue
     * @param Expression      $ifFalse
     *
     * @return self
     */
    public function update(Expression $condition, Expression $ifTrue = null, Expression $ifFalse)
    {
        if ($this->condition === $condition
                && $this->ifTrue === $ifTrue
                && $this->ifFalse === $ifFalse
        ) {
            return $this;
        }

        return new self($condition, $ifTrue, $ifFalse);
    }

    protected function compileCode(&$code)
    {
        $code .= '(';
        $this->condition->compileCode($code);
        $code .= ' ? ';

        if ($this->ifTrue !== null) {
            $this->ifTrue->compileCode($code);
        }

        $code .= ' : ';
        $this->ifFalse->compileCode($code);
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->condition, $this->ifTrue, $this->ifFalse]);
    }

    public function __serialize(): array
    {
        return [$this->condition, $this->ifTrue, $this->ifFalse];
    }
    
    public function unserialize($serialized)
    {
        list($this->condition, $this->ifTrue, $this->ifFalse) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->condition, $this->ifTrue, $this->ifFalse) = $data;
    }

    public function __clone()
    {
        $this->condition = clone $this->condition;
        $this->ifTrue    = $this->ifTrue === null ? null : clone $this->ifTrue;
        $this->ifFalse   = clone $this->ifFalse;
    }
}
