<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * $I[5]
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexExpression extends TraversalExpression
{
    /**
     * @var Expression
     */
    private $indexExpression;
    
    public function __construct(Expression $valueExpression, Expression $indexExpression)
    {
        parent::__construct($valueExpression);
        $this->indexExpression = $indexExpression;
    }
    
    /**
     * @return Expression
     */
    public function getIndexExpression()
    {
        return $this->indexExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkIndex($this);
    }
    
    public function simplify()
    {
        $valueExpression = $this->valueExpression->simplify();
        $indexExpression = $this->indexExpression->simplify();
        
        if ($valueExpression instanceof ValueExpression && $indexExpression instanceof ValueExpression) {
            $value = $valueExpression->getValue();
            $index = $indexExpression->getValue();
            
            return Expression::value($value[$index]);
        }
        
        return $this->update($valueExpression, $this->indexExpression);
    }
    
    /**
     * @return self
     */
    public function update(Expression $valueExpression, Expression $indexExpression)
    {
        if ($this->valueExpression === $valueExpression && $this->indexExpression === $indexExpression) {
            return $this;
        }
        
        return new self($valueExpression, $indexExpression);
    }
    
    protected function updateValueExpression(Expression $valueExpression)
    {
        return new self($valueExpression, $this->indexExpression);
    }
    
    protected function compileCode(&$code)
    {
        $this->valueExpression->compileCode($code);
        $code .= '[';
        $this->indexExpression->compileCode($code);
        $code .= ']';
    }
    
    public function dataToSerialize()
    {
        return $this->indexExpression;
    }
    
    public function unserializedData($data)
    {
        $this->indexExpression = $data;
    }
    
    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
        $this->indexExpression = clone $this->indexExpression;
    }
}