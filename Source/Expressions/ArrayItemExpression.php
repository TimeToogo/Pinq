<?php

namespace Pinq\Expressions;

/**
 * <code>
 * 'test' => 4
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayItemExpression extends Expression
{
    /**
     * @var Expression|null
     */
    private $key;

    /**
     * @var Expression
     */
    private $value;

    /**
     * @var boolean
     */
    private $isReference;

    public function __construct(Expression $key = null, Expression $value, $isReference)
    {
        $this->key         = $key;
        $this->value       = $value;
        $this->isReference = $isReference;
    }

    public function simplify(IEvaluationContext $context = null)
    {
        return $this->update(
                $this->key === null ? null : $this->key->simplify($context),
                $this->value->simplify($context),
                $this->isReference
        );
    }

    /**
     * @return boolean
     */
    public function hasKey()
    {
        return $this->key !== null;
    }

    /**
     * @return Expression|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return Expression
     */
    public function getValue()
    {
        return $this->value;
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

    /**
     * @param Expression $key
     * @param Expression $value
     * @param boolean    $isReference
     *
     * @return self
     */
    public function update(Expression $key = null, Expression $value, $isReference)
    {
        if ($this->key === $key
                && $this->value === $value
                && $this->isReference === $isReference
        ) {
            return $this;
        }

        return new self($key, $value, $isReference);
    }

    protected function compileCode(&$code)
    {
        if ($this->key !== null) {
            $this->key->compileCode($code);
            $code .= ' => ';
        }

        if ($this->isReference) {
            $code .= '&';
        }

        $this->value->compileCode($code);
    }

    public function serialize()
    {
        return serialize([$this->key, $this->value, $this->isReference]);
    }

    public function __serialize(): array
    {
        return [$this->key, $this->value, $this->isReference];
    }

    public function __unserialize(array $data): void
    {
        list($this->key, $this->value, $this->isReference) = $data;
    }

    public function unserialize($serialized)
    {
        list($this->key, $this->value, $this->isReference) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->key   = $this->key !== null ? clone $this->key : null;
        $this->value = clone $this->value;
    }
}
