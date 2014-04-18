<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexExpression extends TraversalExpression
{
    private $IndexExpression;

    public function __construct(Expression $ValueExpression, Expression $IndexExpression)
    {
        parent::__construct($ValueExpression);

        $this->IndexExpression = $IndexExpression;
    }

    /**
     * @return Expression
     */
    public function GetIndexExpression()
    {
        return $this->IndexExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkIndex($this);
    }

    public function Simplify()
    {
        $ValueExpression = $this->ValueExpression->Simplify();
        $IndexExpression = $this->IndexExpression->Simplify();

        if ($ValueExpression instanceof ValueExpression
                && $IndexExpression instanceof ValueExpression) {
            $Value = $ValueExpression->GetValue();
            $Index = $IndexExpression->GetValue();

            return Expression::Value($Value[$Index]);
        }

        return $this->Update(
                $ValueExpression,
                $this->IndexExpression);
    }

    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, Expression $IndexExpression)
    {
        if ($this->ValueExpression === $ValueExpression
                && $this->IndexExpression === $IndexExpression) {
            return $this;
        }

        return new self($ValueExpression, $IndexExpression);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->IndexExpression);
    }

    protected function CompileCode(&$Code)
    {
        $this->ValueExpression->CompileCode($Code);
        $Code .= '[';
        $this->IndexExpression->CompileCode($Code);
        $Code .= ']';
    }
    
    public function __clone()
    {
        $this->ValueExpression = clone $this->ValueExpression;
        $this->IndexExpression = clone $this->IndexExpression;
    }
}
