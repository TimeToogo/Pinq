<?php

namespace Pinq\Expressions;

/**
 * <code>
 * return true
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ReturnExpression extends Expression
{
    /**
     * @var Expression|null
     */
    private $value;

    public function __construct(Expression $value = null)
    {
        $this->value = $value;
    }

    public function asEvaluator(IEvaluationContext $context = null)
    {
        throw static::cannotEvaluate();
    }

    public function simplify(IEvaluationContext $context = null)
    {
        return $this->value === null ? $this : $this->update($this->value->simplify($context));
    }

    /**
     * @return boolean
     */
    public function hasValue()
    {
        return $this->value !== null;
    }

    /**
     * @return Expression|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkReturn($this);
    }

    /**
     * @param Expression $value
     *
     * @return self
     */
    public function update(Expression $value = null)
    {
        if ($this->value === $value) {
            return $this;
        }

        return new self($value);
    }

    protected function compileCode(&$code)
    {
        $code .= 'return ';

        if ($this->value !== null) {
            $this->value->compileCode($code);
        }
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

    public function __unserialize(array $data)
    {
        list($this->value) = $data;
    }

    public function __clone()
    {
        $this->value = $this->value === null ? null : clone $this->value;
    }
}
