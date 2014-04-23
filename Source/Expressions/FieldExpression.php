<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * $I->Field
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FieldExpression extends ObjectOperationExpression
{
    /**
     * @var Expression
     */
    private $nameExpression;
    
    public function __construct(Expression $objectValueExpression, Expression $nameExpression)
    {
        parent::__construct($objectValueExpression);
        $this->nameExpression = $nameExpression;
    }
    
    /**
     * @return Expression
     */
    public function getNameExpression()
    {
        return $this->nameExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkField($this);
    }
    
    public function simplify()
    {
        $valueExpression = $this->valueExpression->simplify();
        $nameExpression = $this->nameExpression->simplify();
        
        if ($valueExpression instanceof ValueExpression && $nameExpression instanceof ValueExpression) {
            $value = $valueExpression->getValue();
            $name = $nameExpression->getValue();
            
            return Expression::value($value->{$name});
        }
        
        return $this->update($valueExpression, $this->nameExpression);
    }
    
    /**
     * @return self
     */
    public function update(Expression $objectValueExpression, Expression $nameExpression)
    {
        if ($this->valueExpression === $objectValueExpression && $this->nameExpression === $nameExpression) {
            return $this;
        }
        
        return new self($objectValueExpression, $nameExpression);
    }
    
    protected function updateValueExpression(Expression $valueExpression)
    {
        return new self($valueExpression, $this->nameExpression);
    }
    
    protected function compileCode(&$code)
    {
        $this->valueExpression->compileCode($code);
        $code .= '->';
        
        if ($this->nameExpression instanceof ValueExpression && \Pinq\Utilities::isNormalSyntaxName($this->nameExpression->getValue())) {
            $code .= $this->nameExpression->getValue();
        }
        else {
            $code .= '{';
            $this->nameExpression->compileCode($code);
            $code .= '}';
        }
    }
    
    public function dataToSerialize()
    {
        return $this->nameExpression;
    }
    
    public function unserializedData($data)
    {
        $this->nameExpression = $data;
    }
    
    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
        $this->nameExpression = clone $this->nameExpression;
    }
}