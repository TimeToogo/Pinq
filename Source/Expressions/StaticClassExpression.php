<?php

namespace Pinq\Expressions;

/**
 * <code>
 * Class::...
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class StaticClassExpression extends Expression
{
    /**
     * @var Expression
     */
    protected $class;

    public function __construct(Expression $class)
    {
        $this->class = $class;
    }

    /**
     * @return Expression
     */
    final public function getClass()
    {
        return $this->class;
    }

    /**
     * @param Expression $classExpression
     *
     * @return static
     */
    final public function updateClass(Expression $classExpression)
    {
        if ($this->class === $classExpression) {
            return $this;
        }

        return $this->updateClassValue($classExpression);
    }

    abstract protected function updateClassValue(Expression $classExpression);

    protected function compileType(&$code, Expression $typeExpression)
    {
        if ($typeExpression instanceof ValueExpression) {
            $code .= $typeExpression->getValue();
        } else {
            $typeExpression->compileCode($code);
        }
    }

    protected function compileCode(&$code)
    {
        $this->compileType($code, $this->class);

        $code .= '::';

        $this->compileMember($code);
    }

    protected function compileMember(&$code)
    {

    }

    final public function serialize()
    {
        return serialize([$this->class, $this->dataToSerialize()]);
    }

    final public function __serialize(): array
    {
        return [$this->class, $this->dataToSerialize()];
    }
    
    abstract protected function dataToSerialize();

    final public function unserialize($serialized)
    {
        list($this->class, $data) = unserialize($serialized);
        $this->unserializeData($data);
    }

    final public function __unserialize(array $data): void
    {
        list($this->class, $dataToUnserialize) = $data;
        $this->unserializeData($dataToUnserialize);
    }


    abstract protected function unserializeData($data);
}
