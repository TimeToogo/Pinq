<?php 

namespace Pinq\Expressions;

use Pinq\Queries;

/**
 * <code>
 * $Traversable->Where(function ($I) { return $I > 5 })->Average();
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SubQueryExpression extends TraversalExpression
{
    /**
     * @var Queries\IRequestQuery
     */
    private $query;
    
    /**
     * @var TraversalExpression
     */
    private $originalExpression;
    
    public function __construct(Expression $valueExpression, Queries\IRequestQuery $query, TraversalExpression $originalExpression)
    {
        parent::__construct($valueExpression);
        $this->query = $query;
        $this->originalExpression = $originalExpression;
    }
    
    /**
     * @return Queries\IRequestQuery
     */
    public function getRequestQuery()
    {
        return $this->query;
    }
    
    /**
     * @return TraversalExpression
     */
    public function getOriginalExpression()
    {
        return $this->originalExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkSubQuery($this);
    }
    
    public function simplify()
    {
        return $this->updateValueExpression($this->valueExpression->simplify());
    }
    
    /**
     * @return self
     */
    public function update(Expression $valueExpression, Queries\IRequestQuery $query, TraversalExpression $originalExpression)
    {
        if ($this->valueExpression === $valueExpression && $this->query === $query && $this->originalExpression === $originalExpression) {
            return $this;
        }
        
        return new self($valueExpression, $query, $originalExpression);
    }
    
    protected function updateValueExpression(Expression $valueExpression)
    {
        return new self($valueExpression, $this->query, $this->originalExpression);
    }
    
    protected function compileCode(&$code)
    {
        $this->originalExpression->compileCode($code);
    }
    
    public function dataToSerialize()
    {
        return [$this->query, $this->originalExpression];
    }
    
    public function unserializedData($data)
    {
        list($this->query, $this->originalExpression) = $data;
    }
    
    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
        $this->query = clone $this->query;
        $this->originalExpression = clone $this->originalExpression;
    }
}