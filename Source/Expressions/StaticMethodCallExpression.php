<?php

namespace Pinq\Expressions;

/**
 * <code>
 * Class::Method('foo')
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StaticMethodCallExpression extends StaticClassExpression
{
    /**
     * @var Expression
     */
    private $name;

    /**
     * @var ArgumentExpression[]
     */
    private $arguments;

    public function __construct(Expression $class, Expression $name, array $arguments = [])
    {
        parent::__construct($class);
        $this->name      = $name;
        $this->arguments = self::verifyAll($arguments, ArgumentExpression::getType());
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
        return $walker->walkStaticMethodCall($this);
    }

    /**
     * @param Expression           $class
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return self
     */
    public function update(Expression $class, Expression $name, array $arguments = [])
    {
        if ($this->class === $class
                && $this->name === $name
                && $this->arguments === $arguments
        ) {
            return $this;
        }

        return new self($class, $name, $arguments);
    }

    protected function compileMember(&$code)
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

    protected function updateClassValue(Expression $class)
    {
        return new self($class, $this->name, $this->arguments);
    }

    protected function dataToSerialize()
    {
        return [$this->name, $this->arguments];
    }

    protected function unserializeData($data)
    {
        list($this->name, $this->arguments) = $data;
    }

    public function __clone()
    {
        $this->class     = clone $this->class;
        $this->name      = clone $this->name;
        $this->arguments = self::cloneAll($this->arguments);
    }
}
