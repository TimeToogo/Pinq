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
        }
        else {
            $this->originExpression = $valueExpression;
            $this->traversalDepth = 1;
        }
    }
    
    /**
     * @param string $expressionType
     * @return boolean
     */
    public final function originatesFrom($expressionType)
    {
        return $this->originExpression instanceof $expressionType;
    }
    
    /**
     * @return Expression
     */
    public final function getOriginExpression()
    {
        return $this->originExpression;
    }
    
    /**
     * @return int
     */
    public final function getTraversalDepth()
    {
        return $this->traversalDepth;
    }
    
    /**
     * @return Expression
     */
    public final function getValueExpression()
    {
        return $this->valueExpression;
    }
    
    /**
     * @return Expression
     */
    public final function updateValue(Expression $valueExpression)
    {
        if ($this->valueExpression === $valueExpression) {
            return $this;
        }
        
        return $this->updateValueExpression($valueExpression);
    }
    
    protected abstract function updateValueExpression(Expression $valueExpression);
    
    public final function serialize()
    {
        return serialize([$this->valueExpression, $this->dataToSerialize()]);
    }
    
    protected abstract function dataToSerialize();
    
    public final function unserialize($serialized)
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
    
    protected abstract function unserializedData($data);
}