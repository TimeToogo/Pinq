<?php

namespace Pinq\Expressions;

/**
 * <code>
 * Class::$field
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StaticFieldExpression extends StaticClassExpression
{
    /**
     * @var Expression
     */
    private $name;

    public function __construct(Expression $class, Expression $name)
    {
        parent::__construct($class);
        $this->name = $name;
    }

    /**
     * @return Expression
     */
    public function getName()
    {
        return $this->name;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkStaticField($this);
    }

    /**
     * @param Expression $class
     * @param Expression $name
     *
     * @return self
     */
    public function update(Expression $class, Expression $name)
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
        $code .= self::variable($this->name)->compile();
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
        $this->name  = clone $this->name;
    }
}
