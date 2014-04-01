<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EmptyExpression extends Expression
{
    private $ValueExpression;
    public function __construct(Expression $ValueExpression)
    {
        $this->ValueExpression = $ValueExpression;
    }

    /**
     * @return Expression
     */
    public function GetValueExpression()
    {
        return $this->ValueExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkEmpty($this);
    }

    public function Simplify()
    {
        $ValueExpression = $this->ValueExpression->Simplify();

        if ($ValueExpression instanceof ValueExpression) {
            $Value = $ValueExpression->GetValue();

            return Expression::Value(empty($Value));
        }

        return $this->Update(
                $ValueExpression);
    }

    /**
     * @return self
     */
    public function Update(Expression $ValueExpression)
    {
        if ($this->ValueExpression === $ValueExpression) {
            return $this;
        }

        return new self($ValueExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'empty(';
        $this->ValueExpression->CompileCode($Code);
        $Code .= ')';
    }
}
