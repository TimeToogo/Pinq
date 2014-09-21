<?php

namespace Pinq\Expressions;

/**
 * <code>
 * new \stdClass()
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NewExpression extends StaticClassExpression
{
    /**
     * @var ArgumentExpression[]
     */
    private $arguments;

    public function __construct(Expression $class, array $arguments = [])
    {
        parent::__construct($class);

        $this->arguments = self::verifyAll($arguments, ArgumentExpression::getType());
    }

    /**
     * @return Expression[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkNew($this);
    }

    /**
     * @param Expression           $class
     * @param ArgumentExpression[] $arguments
     *
     * @return NewExpression
     */
    public function update(Expression $class, array $arguments = [])
    {
        if ($this->class === $class
                && $this->arguments === $arguments
        ) {
            return $this;
        }

        return new self($class, $arguments);
    }

    protected function updateClassValue(Expression $class)
    {
        return new self($class, $this->arguments);
    }

    protected function compileCode(&$code)
    {
        $code .= '(new ';

        $this->compileType($code, $this->class);

        $code .= '(';
        $code .= implode(',', self::compileAll($this->arguments));
        $code .= '))';
    }

    protected function dataToSerialize()
    {
        return [$this->arguments];
    }

    protected function unserializeData($data)
    {
        list($this->arguments) = $data;
    }

    public function __clone()
    {
        $this->class     = clone $this->class;
        $this->arguments = self::cloneAll($this->arguments);
    }
}
