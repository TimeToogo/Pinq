<?php

namespace Pinq\Expressions;

/**
 * <code>
 * throw $I
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ThrowExpression extends Expression
{
    /**
     * @var Expression
     */
    private $exception;

    public function __construct(Expression $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Expression
     */
    public function getException()
    {
        return $this->exception;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkThrow($this);
    }

    /**
     * @param Expression $exception
     *
     * @return self
     */
    public function update(Expression $exception)
    {
        if ($this->exception === $exception) {
            return $this;
        }

        return new self($exception);
    }

    protected function compileCode(&$code)
    {
        $code .= 'throw ';
        $this->exception->compileCode($code);
    }

    public function serialize()
    {
        return serialize($this->exception);
    }

    public function __serialize(): array
    {
        return [$this->exception];
    }
    
    public function unserialize($serialized)
    {
        $this->exception = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->exception) = $data;
    }
    
    public function __clone()
    {
        $this->exception = clone $this->exception;
    }
}
