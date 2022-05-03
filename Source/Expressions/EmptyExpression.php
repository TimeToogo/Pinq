<?php

namespace Pinq\Expressions;

/**
 * <code>
 * empty($I)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EmptyExpression extends Expression
{
    /**
     * @var Expression
     */
    private $value;

    public function __construct(Expression $valueExpression)
    {
        $this->value = $valueExpression;
    }

    /**
     * @return Expression
     */
    public function getValue()
    {
        return $this->value;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkEmpty($this);
    }

    /**
     * @param Expression $value
     *
     * @return self
     */
    public function update(Expression $value)
    {
        if ($this->value === $value) {
            return $this;
        }

        return new self($value);
    }

    protected function compileCode(&$code)
    {
        $code .= 'empty(';
        $this->value->compileCode($code);
        $code .= ')';
    }

    public function serialize()
    {
        return serialize($this->value);
    }

    public function __serialize(): array
    {
        return [$this->value];
    }
    
    public function unserialize($serialized)
    {
        $this->value = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->value) = $data;
    }
    
    public function __clone()
    {
        $this->value = clone $this->value;
    }
}
