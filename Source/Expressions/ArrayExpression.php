<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * [1, 2, 'test' => 4]
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayExpression extends Expression
{
    /**
     * @var Expression[] 
     */
    private $keyExpressions;
    
    /**
     * @var Expression[] 
     */
    private $valueExpressions;
    
    public function __construct(array $keyExpressions, array $valueExpressions)
    {
        ksort($keyExpressions);
        ksort($valueExpressions);
        
        if (array_keys($keyExpressions) !== array_keys($valueExpressions)) {
            throw new \Pinq\PinqException(
                    'The supplied key expression array keys must match the keys of the value expression array: (%s) !== (%s)',
                    implode(', ', array_keys($keyExpressions)),
                    implode(', ', array_keys($valueExpressions)));
        }
        
        $this->keyExpressions = $keyExpressions;
        $this->valueExpressions = $valueExpressions;
    }
    
    /**
     * @return Expression|null[]
     */
    public function getKeyExpressions()
    {
        return $this->keyExpressions;
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
        return $walker->walkArray($this);
    }
    
    public function simplify()
    {
        $keyExpressions = [];
        
        foreach ($this->keyExpressions as $key => $keyExpression) {
            $keyExpressions[$key] = $keyExpression === null ? null : $keyExpression->simplify();
        }
        
        $valueExpressions = self::simplifyAll($this->valueExpressions);
        
        if (self::allOfType($keyExpressions, ValueExpression::getType(), true) && self::allOfType($valueExpressions, ValueExpression::getType())) {
            $resolvedArray = [];
            
            foreach ($keyExpressions as $valueKey => $keyExpression) {
                if ($keyExpression === null) {
                    $resolvedArray[] = $valueExpressions[$valueKey]->getValue();
                }
                else {
                    $resolvedArray[$keyExpression->getValue()] = $valueExpressions[$valueKey]->getValue();
                }
            }
            
            return Expression::value($resolvedArray);
        }
        
        return $this->update($keyExpressions, $valueExpressions);
    }
    
    /**
     * @return self
     */
    public function update(array $keyExpressions, array $valueExpressions)
    {
        if ($this->valueExpressions === $valueExpressions && $this->keyExpressions === $keyExpressions) {
            return $this;
        }
        
        return new self($keyExpressions, $valueExpressions);
    }
    
    protected function compileCode(&$code)
    {
        $code .= '[';
        $first = true;
        
        foreach ($this->keyExpressions as $key => $keyExpression) {
            if ($first) {
                $first = false;
            }
            else {
                $code .= ', ';
            }
            
            if ($keyExpression !== null) {
                $keyExpression->compileCode($code);
                $code .= ' => ';
            }
            
            $this->valueExpressions[$key]->compileCode($code);
        }
        
        $code .= ']';
    }
    
    public function serialize()
    {
        return serialize([$this->keyExpressions, $this->valueExpressions]);
    }
    
    public function unserialize($serialized)
    {
        list($this->keyExpressions, $this->valueExpressions) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->keyExpressions = self::cloneAll($this->keyExpressions);
        $this->valueExpressions = self::cloneAll($this->valueExpressions);
    }
}