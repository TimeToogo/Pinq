<?php

namespace Pinq\Expressions;

/**
 * <code>
 * strlen($I)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionCallExpression extends Expression
{
    /**
     * @var Expression
     */
    private $name;

    /**
     * @var ArgumentExpression[]
     */
    private $arguments;

    public function __construct(Expression $nameExpression, array $argumentExpressions = [])
    {
        $this->name      = $nameExpression;
        $this->arguments = self::verifyAll($argumentExpressions, ArgumentExpression::getType());
    }

    /**
     * @return Expression
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArgumentExpression[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkFunctionCall($this);
    }

    /**
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return self
     */
    public function update(Expression $name, array $arguments = [])
    {
        if ($this->name === $name
                && $this->arguments === $arguments
        ) {
            return $this;
        }

        return new self($name, $arguments);
    }

    protected function compileCode(&$code)
    {
        if ($this->name instanceof ValueExpression) {
            $code .= $this->name->getValue();
        } else {
            $this->name->compileCode($code);
        }

        $code .= '(';
        $code .= implode(',', self::compileAll($this->arguments));
        $code .= ')';
    }

    public function serialize()
    {
        return serialize([$this->name, $this->arguments]);
    }

    public function __serialize(): array
    {
        return [$this->name, $this->arguments];
    }
    
    public function unserialize($serialized)
    {
        list($this->name, $this->arguments) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->name, $this->arguments) = $data;
    }
    
    public function __clone()
    {
        $this->name      = clone $this->name;
        $this->arguments = self::cloneAll($this->arguments);
    }
}
