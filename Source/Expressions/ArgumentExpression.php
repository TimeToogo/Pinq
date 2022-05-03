<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $i, ...&$i
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArgumentExpression extends Expression
{
    /**
     * @var Expression
     */
    private $value;

    /**
     * @var boolean
     */
    private $isUnpacked;

    public function __construct(
            Expression $value,
            $isUnpacked = false
    ) {
        $this->value      = $value;
        $this->isUnpacked = $isUnpacked;
    }

    public function asEvaluator(IEvaluationContext $context = null)
    {
        throw static::cannotEvaluate();
    }

    public function simplify(IEvaluationContext $context = null)
    {
        return $this->update($this->value->simplify($context), $this->isUnpacked);
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkArgument($this);
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
    public function isUnpacked()
    {
        return $this->isUnpacked;
    }

    /**
     * @param Expression $value
     * @param boolean    $isUnpacked
     *
     * @return self
     */
    public function update(Expression $value, $isUnpacked)
    {
        if ($this->value === $value
                && $this->isUnpacked === $isUnpacked
        ) {
            return $this;
        }

        return new self($value, $isUnpacked);
    }

    protected function compileCode(&$code)
    {
        if ($this->isUnpacked) {
            $code .= '...';
        }

        $this->value->compileCode($code);
    }

    public function serialize()
    {
        return serialize(
                [
                        $this->value,
                        $this->isUnpacked
                ]
        );
    }

    public function __serialize(): array
    {
        return [$this->value, $this->isUnpacked];
    }

    public function unserialize($serialized)
    {
        list(
                $this->value,
                $this->isUnpacked) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->value, $this->isUnpacked) = $data;
    }
    
    public function __clone()
    {
        $this->value = clone $this->value;
    }
}
