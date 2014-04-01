<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class StaticMethodCallExpression extends Expression
{
    private $ClassExpression;
    private $NameExpression;
    private $ArgumentExpressions;
    public function __construct(Expression $ClassExpression, Expression $NameExpression, array $ArgumentExpressions = [])
    {
        $this->ClassExpression = $ClassExpression;
        $this->NameExpression = $NameExpression;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }

    /**
     * @return Expression
     */
    public function GetClassExpression()
    {
        return $this->ClassExpression;
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
        return $Walker->WalkStaticMethodCall($this);
    }

    public function Simplify()
    {
        return $this->Update(
                $this->ClassExpression->Simplify(),
                $this->NameExpression->Simplify(),
                self::SimplifyAll($this->ArgumentExpressions));
    }

    /**
     * @return self
     */
    public function Update(Expression $ClassExpression, Expression $NameExpression, array $ArgumentExpressions = [])
    {
        if ($this->ClassExpression === $ClassExpression
                && $this->NameExpression === $NameExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }

        return new self($ClassExpression, $NameExpression, $ArgumentExpressions);
    }

    protected function CompileCode(&$Code)
    {
        if ($this->ClassExpression instanceof ValueExpression) {
            $Code .= $this->ClassExpression->GetValue();
        } 
        else {
            $this->ClassExpression->CompileCode($Code);
        }

        $Code .= '::';

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
}
