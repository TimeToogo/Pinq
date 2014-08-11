<?php

namespace Pinq\Expressions;

/**
 * <code>
 * use (&$i)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClosureUsedVariableExpression extends Expression
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $isReference;

    public function __construct($name, $isReference)
    {
        $this->name        = $name;
        $this->isReference = $isReference;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @param string $name
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

    public function unserialize($serialized)
    {
        list(
                $this->name,
                $this->isReference) = unserialize($serialized);
    }

    public function __clone()
    {

    }
}