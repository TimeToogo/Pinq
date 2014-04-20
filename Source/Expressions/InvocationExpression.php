<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends TraversalExpression
{
    private $ArgumentExpressions;

    public function __construct(Expression $ValueExpression, array $ArgumentExpressions)
    {
        parent::__construct($ValueExpression);

        $this->ArgumentExpressions = $ArgumentExpressions;
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
        return $Walker->WalkInvocation($this);
    }

    public function Simplify()
    {
        $ValueExpression = $this->ValueExpression->Simplify();
        $ArgumentExpressions = self::SimplifyAll($this->ArgumentExpressions);
        
        if($ValueExpression instanceof ValueExpression
                && self::AllOfType($ArgumentExpressions, ValueExpression::GetType())) {
            $ObjectValue = $ValueExpression->GetValue();
            $ArgumentValues = [];
            foreach ($ArgumentExpressions as $ArgumentExpression) {
                $ArgumentValues[] = $ArgumentExpression->GetValue();
            }

            return Expression::Value(call_user_func_array($ObjectValue, $ArgumentValues));
        }

        return $this->Update(
                $ValueExpression,
                $ArgumentExpressions);
    }

    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, array $ArgumentExpressions)
    {
        if ($this->ValueExpression === $ValueExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }

        return new self($ValueExpression, $ArgumentExpressions);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->ArgumentExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $this->ValueExpression->CompileCode($Code);
        $Code .= '(';
        $Code .= implode(',', self::CompileAll($this->ArgumentExpressions));
        $Code .= ')';
    }
    
    public function DataToSerialize()
    {
        return $this->ArgumentExpressions;
    }
    
    public function UnserializedData($Data)
    {
        $this->ArgumentExpressions = $Data;
    }
    
    public function __clone()
    {
        $this->ValueExpression = clone $this->ValueExpression;
        $this->ArgumentExpressions = self::CloneAll($this->ArgumentExpressions);
    }
}
