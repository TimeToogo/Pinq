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
    private $ValueExpressions;
    
    public function __construct(array $ValueExpressions)
    {
        if(count($ValueExpressions) === 0) {
            throw new \Pinq\PinqException('Invalid amount of value expressions for %s: must be greater than 0', __CLASS__);
        }
        $this->ValueExpressions = $ValueExpressions;
    }

    /**
     * @return Expression[]
     */
    public function GetValueExpressions()
    {
        return $this->ValueExpressions;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkIsset($this);
    }

    public function Simplify()
    {
        $ValueExpressions = self::SimplifyAll($this->ValueExpressions);
        
        foreach($ValueExpressions as $Key => $ValueExpression) {
            $IsConstantValue = $ValueExpression instanceof ValueExpression;
            
            if($IsConstantValue && $ValueExpression->GetValue() === null) {
                return Expression::Value(false);
            }
            else if($IsConstantValue) {
                unset($ValueExpressions[$Key]);
            }
        }
        if(self::AllOfType($ValueExpressions, ValueExpression::GetType())) {
            return Expression::Value(true);
        }
        
        return $this->Update($ValueExpressions);
    }

    /**
     * @return self
     */
    public function Update(array $ValueExpressions)
    {
        if ($this->ValueExpressions === $ValueExpressions) {
            return $this;
        }

        return new self($ValueExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'isset(';
        $Code .= implode(',', self::CompileAll($this->ValueExpressions));
        $Code .= ')';
    }
    
    public function serialize()
    {
        return serialize($this->ValueExpressions);
    }
    
    public function unserialize($Serialized)
    {
        $this->ValueExpressions = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->ValueExpressions = self::CloneAll($this->ValueExpressions);
    }
}
