<?php

namespace Pinq\Expressions;

/**
 * Represents acting on a value (properties, methods, indexer...)
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class TraversalExpression extends Expression
{
    /**
     * @var Expression
     */
    protected $valueExpression;

    /**
     * @var Expression
     */
    protected $originExpression;

    /**
     * @var int
     */
    protected $traversalDepth;

    public function __construct(Expression $valueExpression)
    {
        $this->valueExpression = $valueExpression;

        if ($valueExpression instanceof self) {
            $this->originExpression = $valueExpression->originExpression;
            $this->traversalDepth = $valueExpression->traversalDepth + 1;
        } else {
            $this->originExpression = $valueExpression;
            $this->traversalDepth = 1;
        }
    }

    /**
     * @param string $expressionType
     * @return boolean
     */
    final public function originatesFrom($expressionType)
    {
        return $this->originExpression instanceof $expressionType;
    }

    /**
     * @return Expression
     */
    final public function getOriginExpression()
    {
        return $this->originExpression;
    }

    /**
     * @return int
     */
    final public function getTraversalDepth()
    {
        return $this->traversalDepth;
    }

    /**
     * @return Expression
     */
    final public function getValueExpression()
    {
        return $this->valueExpression;
    }

    /**
     * @return Expression
     */
    final public function updateValue(Expression $valueExpression)
    {
        if ($this->valueExpression === $valueExpression) {
            return $this;
        }

        return $this->updateValueExpression($valueExpression);
    }

    abstract protected function updateValueExpression(Expression $valueExpression);

    final public function serialize()
    {
        return serialize([$this->valueExpression, $this->dataToSerialize()]);
    }

    abstract protected function dataToSerialize();

    final public function unserialize($serialized)
    {
        list($this->valueExpression, $childData) = unserialize($serialized);
        $this->unserializedData($childData);
        $this->traversalDepth = 1;
        $this->originExpression = $this->valueExpression;

        while ($this->originExpression instanceof TraversalExpression) {
            $this->traversalDepth++;
            $this->originExpression = $this->originExpression->getValueExpression();
        }
    }

    abstract protected function unserializedData($data);
}
