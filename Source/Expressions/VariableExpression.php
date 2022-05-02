<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I
 * </code>
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class VariableExpression extends Expression
{
    /**
     * @var Expression
     */
    private $name;

    public function __construct(Expression $name)
    {
        $this->name = $name;
    }

    public function asEvaluator(IEvaluationContext $context = null)
    {
        $nameExpression = $this->name;
        if ($nameExpression instanceof ValueExpression) {
            return new VariableEvaluator($nameExpression->getValue(), $context);
        }

        return parent::asEvaluator($context);
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkVariable($this);
    }

    /**
     * @return Expression
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Expression $name
     *
     * @return self
     */
    public function update(Expression $name)
    {
        if ($this->name === $name) {
            return $this;
        }

        return new self($name);
    }

    protected function compileCode(&$code)
    {
        if ($this->name instanceof ValueExpression && self::isNormalSyntaxName($this->name->getValue())) {
            $code .= '$' . $this->name->getValue();
        } else {
            $code .= '${';
            $this->name->compileCode($code);
            $code .= '}';
        }
    }

    public function serialize()
    {
        return serialize($this->name);
    }

    public function __serialize(): array
    {
        return [$this->name];
    }

    public function unserialize($serialized)
    {
        $this->name = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->name) = $data;
    }

    public function __clone()
    {
        $this->name = clone $this->name;
    }
}
