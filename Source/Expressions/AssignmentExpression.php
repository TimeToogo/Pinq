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
    private $assignToExpression;
    
    /**
     * @var int
     */
    private $operator;
    
    /**
     * @var Expression
     */
    private $assignmentValueExpression;
    
    public function __construct(Expression $assignToExpression, $operator, Expression $assignmentValueExpression)
    {
        $this->assignToExpression = $assignToExpression;
        $this->operator = $operator;
        $this->assignmentValueExpression = $assignmentValueExpression;
    }
    
    /**
     * @return Expression
     */
    public function getAssignToExpression()
    {
        return $this->assignToExpression;
    }
    
    /**
     * @return string The assignment operator
     */
    public function getOperator()
    {
        return $this->operator;
    }
    
    /**
     * @return Expression
     */
    public function getAssignmentValueExpression()
    {
        return $this->assignmentValueExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkAssignment($this);
    }
    
    public function simplify()
    {
        return $this->update(
                $this->assignToExpression->simplify(),
                $this->operator,
                $this->assignmentValueExpression->simplify());
    }
    
    /**
     * @return self
     */
    public function update(Expression $assignToExpression, $operator, Expression $assignmentValueExpression)
    {
        if ($this->assignToExpression === $assignToExpression && $this->operator === $operator && $this->assignmentValueExpression === $assignmentValueExpression) {
            return $this;
        }
        
        return new self($assignToExpression, $operator, $assignmentValueExpression);
    }
    
    protected function compileCode(&$code)
    {
        $this->assignToExpression->compileCode($code);
        $code .= ' ' . $this->operator . ' ';
        $this->assignmentValueExpression->compileCode($code);
    }
    
    public function serialize()
    {
        return serialize([$this->assignToExpression, $this->operator, $this->assignmentValueExpression]);
    }
    
    public function unserialize($serialized)
    {
        list($this->assignToExpression, $this->operator, $this->assignmentValueExpression) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->assignToExpression = clone $this->assignToExpression;
        $this->assignmentValueExpression = clone $this->assignmentValueExpression;
    }
}