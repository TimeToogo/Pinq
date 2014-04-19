<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class TernaryExpression extends Expression
{
    private $ConditionExpression;
    private $IfTrueExpression;
    private $IfFalseExpression;

    public function __construct(
            Expression $ConditionExpression,
            Expression $IfTrueExpression = null,
            Expression $IfFalseExpression) {
        $this->ConditionExpression = $ConditionExpression;
        $this->IfTrueExpression = $IfTrueExpression;
        $this->IfFalseExpression = $IfFalseExpression;
    }

    /**
     * @return Expression
     */
    public function GetConditionExpression()
    {
        return $this->ConditionExpression;
    }

    /**
     * @return boolean
     */
    public function HasIfTrueExpression()
    {
        return $this->IfTrueExpression !== null;
    }

    /**
     * @return Expression
     */
    public function GetIfTrueExpression()
    {
        return $this->IfTrueExpression;
    }

    /**
     * @return Expression
     */
    public function GetIfFalseExpression()
    {
        return $this->IfFalseExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkTernary($this);
    }

    public function Simplify()
    {
        $ConditionExpression = $this->ConditionExpression->Simplify();
        $IfTrueExpression = $this->IfTrueExpression->Simplify();
        $IfFalseExpression = $this->IfFalseExpression->Simplify();

        if ($ConditionExpression instanceof ValueExpression) {
            return $ConditionExpression->GetValue() ?
                    $IfTrueExpression : $IfFalseExpression;
        }

        return $this->Update(
                $ConditionExpression,
                $IfTrueExpression,
                $IfFalseExpression);
    }

    /**
     * @return self
     */
    public function Update(
            Expression $ConditionExpression,
            Expression $IfTrueExpression,
            Expression $IfFalseExpression) {
        if ($this->ConditionExpression === $ConditionExpression
                && $this->IfTrueExpression === $IfTrueExpression
                && $this->IfFalseExpression === $IfFalseExpression) {
            return $this;
        }

        return new self($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= '(';
        
        $this->ConditionExpression->CompileCode($Code);
        $Code .= ' ? ';
        if ($this->IfTrueExpression !== null) {
            $this->IfTrueExpression->CompileCode($Code);
        }
        $Code .= ' : ';
        $this->IfFalseExpression->CompileCode($Code);
        
        $Code .= ')';
    }
    
    public function serialize()
    {
        return serialize([$this->ConditionExpression, $this->IfTrueExpression, $this->IfFalseExpression]);
    }
    
    public function unserialize($Serialized)
    {
        list($this->ConditionExpression, $this->IfTrueExpression, $this->IfFalseExpression) = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->ConditionExpression = clone $this->ConditionExpression;
        $this->IfTrueExpression = clone $this->IfTrueExpression;
        $this->IfFalseExpression = clone $this->IfFalseExpression;
    }
}
