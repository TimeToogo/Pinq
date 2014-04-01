<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FieldExpression extends ObjectOperationExpression
{
    private $NameExpression;

    public function __construct(Expression $ObjectValueExpression, Expression $NameExpression)
    {
        parent::__construct($ObjectValueExpression);

        $this->NameExpression = $NameExpression;
    }

    /**
     * @return Expression
     */
    public function GetNameExpression()
    {
        return $this->NameExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkField($this);
    }

    public function Simplify()
    {
        $ValueExpression = $this->ValueExpression->Simplify();
        $NameExpression = $this->NameExpression->Simplify();

        if ($ValueExpression instanceof ValueExpression
                && $NameExpression instanceof ValueExpression) {
            $Value = $ValueExpression->GetValue();
            $Name = $NameExpression->GetValue();

            return Expression::Value($Value->{$Name});
        }

        return $this->Update(
                $ValueExpression,
                $this->NameExpression);
    }
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, Expression $NameExpression)
    {
        if ($this->ValueExpression === $ObjectValueExpression
                && $this->NameExpression === $NameExpression) {
            return $this;
        }

        return new self($ObjectValueExpression, $NameExpression);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->NameExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= '->{';
        $this->ValueExpression->CompileCode($Code);
        $Code .= '}';
    }
}
