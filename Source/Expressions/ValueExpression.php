<?php

namespace Pinq\Expressions;

/**
 * <code>
 * 1, 'foo', [], null etc
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ValueExpression extends Expression
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkValue($this);
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function update($value)
    {
        if ($this->value === $value) {
            return $this;
        }

        return new self($value);
    }

    protected function compileCode(&$code)
    {
        if (is_scalar($this->value)
                || $this->value === null
                || is_array($this->value)
                || is_object($this->value) && method_exists($this->value, '__set_state')
        ) {
            $code .= var_export($this->value, true);
        } elseif ($this->value instanceof \Closure) {
            throw new \Pinq\PinqException('Cannot compile value expression: value of type \\Closure cannot be serialzed');
        } else {
            $code .= 'unserialize(' . var_export(serialize($this->value), true) . ')';
        }
    }

    public function serialize()
    {
        return serialize([$this->value]);
    }

    public function unserialize($serialized)
    {
        list($this->value) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
