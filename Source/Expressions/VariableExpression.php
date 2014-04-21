<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableExpression extends Expression
{
    /**
     * @var Expression
     */
    private $NameExpression;
    
    public function __construct(Expression $NameExpression)
    {
        $this->NameExpression = $NameExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkVariable($this);
    }

    public function Simplify()
    {
        return $this->Update($this->NameExpression->Simplify());
    }

    /**
     * @return Expression
     */
    public function GetNameExpression()
    {
        return $this->NameExpression;
    }

    public function Update(Expression $NameExpression)
    {
        if ($this->NameExpression === $NameExpression) {
            return $this;
        }

        return new self($NameExpression);
    }

    protected function CompileCode(&$Code)
    {
        if($this->NameExpression instanceof ValueExpression && \Pinq\Utilities::IsNormalSyntaxName($this->NameExpression->GetValue())) {
            $Code .= '$' . $this->NameExpression->GetValue(); 
        }
        else {
            $Code .= '${';
            $this->NameExpression->CompileCode($Code);
            $Code .= '}';
        }
    }
    
    public function serialize()
    {
        return serialize($this->NameExpression);
    }
    
    public function unserialize($Serialized)
    {
        $this->NameExpression = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->NameExpression = clone $this->NameExpression;
    }
}
