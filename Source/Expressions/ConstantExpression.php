<?php

namespace Pinq\Expressions;

/**
 * <code>
 * SORT_ASC
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ConstantExpression extends Expression
{
    /**
     * @var string
     */
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkConstant($this);
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function update($name)
    {
        if ($this->name === $name) {
            return $this;
        }

        return new self($name);
    }

    protected function compileCode(&$code)
    {
        $code .= $this->name;
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

    }
}
