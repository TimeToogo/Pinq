<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $Variable += 5
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AssignmentExpression extends Expression
{
    /**
     * @var Expression
     */
    private $AssignToExpression;
    
    /**
     * @var int
     */
    private $Operator;
    
    /**
     * @var Expression
     */
    private $AssignmentValueExpression;
    
    public function __construct(Expression $AssignToExpression, $Operator, Expression $AssignmentValueExpression)
    {
        $this->AssignToExpression = $AssignToExpression;
        $this->Operator = $Operator;
        $this->AssignmentValueExpression = $AssignmentValueExpression;
    }

    /**
     * @return Expression
     */
    public function GetAssignToExpression()
    {
        return $this->AssignToExpression;
    }

    /**
     * @return string The assignment operator
     */
    public function GetOperator()
    {
        return $this->Operator;
    }

    /**
     * @return Expression
     */
    public function GetAssignmentValueExpression()
    {
        return $this->AssignmentValueExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkAssignment($this);
    }

    public function Simplify()
    {
        return $this->Update(
                $this->AssignToExpression->Simplify(),
                $this->Operator,
                $this->AssignmentValueExpression->Simplify());
    }

    /**
     * @return self
     */
    public function Update(Expression $AssignToExpression, $Operator, Expression $AssignmentValueExpression)
    {
        if ($this->AssignToExpression === $AssignToExpression
                && $this->Operator === $Operator
                && $this->AssignmentValueExpression === $AssignmentValueExpression) {
            return $this;
        }

        return new self($AssignToExpression, $Operator, $AssignmentValueExpression);
    }

    protected function CompileCode(&$Code)
    {
        $this->AssignToExpression->CompileCode($Code);
        $Code .= ' ' .  $this->Operator . ' ';
        $this->AssignmentValueExpression->CompileCode($Code);
    }
    
    public function serialize()
    {
        return serialize([$this->AssignToExpression, $this->Operator, $this->AssignmentValueExpression]);
    }
    
    public function unserialize($Serialized)
    {
        list($this->AssignToExpression, $this->Operator, $this->AssignmentValueExpression) = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->AssignToExpression = clone $this->AssignToExpression;
        $this->AssignmentValueExpression = clone $this->AssignmentValueExpression;
    }
}
