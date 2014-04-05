<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class MethodCallExpression extends ObjectOperationExpression
{
    private $NameExpression;
    private $ArgumentExpressions;

    public function __construct(Expression $ObjectValueExpression, Expression $NameExpression, array $ArgumentExpressions = [])
    {
        parent::__construct($ObjectValueExpression);

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
        return $Walker->WalkMethodCall($this);
    }

    public function Simplify()
    {
        $ValueExpression = $this->ValueExpression->Simplify();
        $NameExpression = $this->NameExpression->Simplify();
        $ArgumentExpressions = self::SimplifyAll($this->ArgumentExpressions);

        if($ValueExpression instanceof ValueExpression
                && $NameExpression instanceof ValueExpression
                && self::AllOfType($ArgumentExpressions, ValueExpression::GetType())) {
            $ObjectValue = $ValueExpression->GetValue();
            $Name = $NameExpression->GetValue();

            $ArgumentValues = [];
            foreach ($ArgumentExpressions as $ArgumentExpression) {
                $ArgumentValues[] = $ArgumentExpression->GetValue();
            }

            return Expression::Value(call_user_func_array([$ObjectValue, $Name], $ArgumentValues));
        }

        return $this->Update(
                $ValueExpression,
                $this->NameExpression,
                $ArgumentExpressions);
    }

    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, Expression $NameExpression, array $ArgumentExpressions)
    {
        if ($this->ValueExpression === $ObjectValueExpression
                && $this->NameExpression === $NameExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }

        return new self($ObjectValueExpression, $NameExpression, $ArgumentExpressions);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->NameExpression, $this->ArgumentExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $this->ValueExpression->CompileCode($Code);
        $Code .= '->';
        if($this->NameExpression instanceof ValueExpression) {
            $Code .= $this->NameExpression->GetValue(); 
        }
        else {
            $Code .= '{';
            $this->NameExpression->CompileCode($Code);
            $Code .= '}';
        }
        $Code .= '(';
        $Code .= implode(',', self::CompileAll($this->ArgumentExpressions));
        $Code .= ')';
    }
}
