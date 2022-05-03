<?php

namespace Pinq\Expressions;

/**
 * <code>
 * use (&$i)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClosureUsedVariableExpression extends NamedParameterExpression
{
    /**
     * @var boolean
     */
    private $isReference;

    public function __construct($name, $isReference)
    {
        parent::__construct($name);
        $this->isReference = $isReference;
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
        return $walker->walkClosureUsedVariable($this);
    }

    /**
     * @param string  $name
     * @param boolean $isReference
     *
     * @return ClosureUsedVariableExpression
     */
    public function update($name, $isReference)
    {
        if ($this->name === $name && $this->isReference === $isReference) {
            return $this;
        }

        return new self($name, $isReference);
    }

    protected function compileCode(&$code)
    {
        if ($this->isReference) {
            $code .= '&';
        }

        $code .= '$' . $this->name;
    }

    public function serialize()
    {
        return serialize(
                [
                        $this->name,
                        $this->isReference
                ]
        );
    }

    public function __serialize(): array
    {
        return [$this->name, $this->isReference];
    }
    
    public function unserialize($serialized)
    {
        list(
                $this->name,
                $this->isReference) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->name, $this->isReference) = $data;
    }

    public function __clone()
    {

    }
}
