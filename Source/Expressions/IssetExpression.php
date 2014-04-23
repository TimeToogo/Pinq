<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * isset($I, $B)
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IssetExpression extends Expression
{
    /**
     * @var Expression[]
     */
    private $valueExpressions;
    
    public function __construct(array $valueExpressions)
    {
        if (count($valueExpressions) === 0) {
            throw new \Pinq\PinqException(
                    'Invalid amount of value expressions for %s: must be greater than 0',
                    __CLASS__);
        }
        
        $this->valueExpressions = $valueExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function getValueExpressions()
    {
        return $this->valueExpressions;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkIsset($this);
    }
    
    public function simplify()
    {
        $valueExpressions = self::simplifyAll($this->valueExpressions);
        
        foreach ($valueExpressions as $key => $valueExpression) {
            $isConstantValue = $valueExpression instanceof ValueExpression;
            
            if ($isConstantValue && $valueExpression->getValue() === null) {
                return Expression::value(false);
            }
            else if ($isConstantValue) {
                unset($valueExpressions[$key]);
            }
        }
        
        if (self::allOfType($valueExpressions, ValueExpression::getType())) {
            return Expression::value(true);
        }
        
        return $this->update($valueExpressions);
    }
    
    /**
     * @return self
     */
    public function update(array $valueExpressions)
    {
        if ($this->valueExpressions === $valueExpressions) {
            return $this;
        }
        
        return new self($valueExpressions);
    }
    
    protected function compileCode(&$code)
    {
        $code .= 'isset(';
        $code .= implode(',', self::compileAll($this->valueExpressions));
        $code .= ')';
    }
    
    public function serialize()
    {
        return serialize($this->valueExpressions);
    }
    
    public function unserialize($serialized)
    {
        $this->valueExpressions = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->valueExpressions = self::cloneAll($this->valueExpressions);
    }
}