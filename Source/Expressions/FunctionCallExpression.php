<?php

namespace Pinq\Expressions;

/**
 * <code>
 * strlen($I)
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCallExpression extends Expression
{
    /**
     * @var Expression
     */
    private $NameExpression;
    
    /**
     * @var Expression[]
     */
    private $ArgumentExpressions;
    
    public function __construct(Expression $NameExpression, array $ArgumentExpressions = [])
    {
        $this->NameExpression = $NameExpression;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }

    /**
     * @return Expression
     */
    public function GetNameExpression()
    {
        return $this->NameExpression;
    }

    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions()
    {
        return $this->ArgumentExpressions;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkFunctionCall($this);
    }

    public function Simplify()
    {
        //TODO: Add a whitelist of deteministic and side-effect free functions.
        return $this->Update(
                $this->NameExpression->Simplify(),
                self::SimplifyAll($this->ArgumentExpressions));
    }

    /**
     * @return self
     */
    public function Update(Expression $NameExpression, array $ArgumentExpressions = [])
    {
        if ($this->NameExpression === $NameExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }

        return new self($NameExpression, $ArgumentExpressions);
    }

    protected function CompileCode(&$Code)
    {
        if ($this->NameExpression instanceof ValueExpression) {
            $Code .= $this->NameExpression->GetValue();
        } 
        else {
            $this->NameExpression->CompileCode($Code);
        }
        $Code .= '(';
        $Code .= implode(',', self::CompileAll($this->ArgumentExpressions));
        $Code .= ')';
    }
    
    public function serialize()
    {
        return serialize([$this->NameExpression, $this->ArgumentExpressions]);
    }
    
    public function unserialize($Serialized)
    {
        list($this->NameExpression, $this->ArgumentExpressions) = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->NameExpression = clone $this->NameExpression;
        $this->ArgumentExpressions = self::CloneAll($this->ArgumentExpressions);
    }
}
