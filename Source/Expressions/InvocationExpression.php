<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I('foo')
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InvocationExpression extends TraversalExpression
{
    /**
     * @var ArgumentExpression[]
     */
    private $arguments;

    public function __construct(Expression $value, array $arguments)
    {
        parent::__construct($value);
        $this->arguments = self::verifyAll($arguments, ArgumentExpression::getType());
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
        return $walker->walkInvocation($this);
    }

    /**
     * @param Expression           $value
     * @param ArgumentExpression[] $arguments
     *
     * @return self
     */
    public function update(Expression $value, array $arguments)
    {
        if ($this->value === $value
                && $this->arguments === $arguments
        ) {
            return $this;
        }

        return new self($value, $arguments);
    }

    protected function updateValueExpression(Expression $value)
    {
        return new self($value, $this->arguments);
    }

    protected function compileCode(&$code)
    {
        $this->value->compileCode($code);
        $code .= '(';
        $code .= implode(',', self::compileAll($this->arguments));
        $code .= ')';
    }

    public function dataToSerialize()
    {
        return $this->arguments;
    }

    public function unserializeData($data)
    {
        $this->arguments = $data;
    }

    public function __clone()
    {
        $this->value     = clone $this->value;
        $this->arguments = self::cloneAll($this->arguments);
    }
}
