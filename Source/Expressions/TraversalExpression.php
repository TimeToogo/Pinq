<?php

namespace Pinq\Expressions;

/**
 * Represents acting on a value (properties, methods, indexer...)
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class TraversalExpression extends Expression
{
    /**
     * @var Expression
     */
    protected $value;

    public function __construct(Expression $value)
    {
        $this->value = $value;
    }

    /**
     * @return Expression
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Expression $value
     *
     * @return static
     */
    final public function updateValue(Expression $value)
    {
        if ($this->value === $value) {
            return $this;
        }

        return $this->updateValueExpression($value);
    }

    abstract protected function updateValueExpression(Expression $valueExpression);

    final public function serialize()
    {
        return serialize([$this->value, $this->dataToSerialize()]);
    }

    final public function __serialize(): array
    {
        return [$this->value, $this->dataToSerialize()];
    }

    abstract protected function dataToSerialize();

    final public function unserialize($serialized)
    {
        list($this->value, $childData) = unserialize($serialized);
        $this->unserializeData($childData);
    }

    final public function __unserialize(array $data): void
    {
        list($this->value, $childData) = $data;
        $this->unserializeData($childData);
    }

    abstract protected function unserializeData($data);
}
