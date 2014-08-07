<?php

namespace Pinq\Expressions;

/**
 * <code>
 * Class:constant
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClassConstantExpression extends StaticClassExpression
{
    /**
     * @var string
     */
    private $name;

    public function __construct(Expression $class, $name)
    {
        parent::__construct($class);

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
        return $walker->walkClassConstant($this);
    }

    /**
     * @param Expression $class
     * @param string     $name
     *
     * @return self
     */
    public function update(Expression $class, $name)
    {
        if ($this->class === $class
                && $this->name === $name
        ) {
            return $this;
        }

        return new self($class, $name);
    }

    protected function updateClassValue(Expression $class)
    {
        return new self($class, $this->name);
    }

    protected function compileMember(&$code)
    {
        $code .= $this->name;
    }

    protected function dataToSerialize()
    {
        return $this->name;
    }

    protected function unserializeData($data)
    {
        $this->name = $data;
    }

    public function __clone()
    {
        $this->class = clone $this->class;
    }
}
