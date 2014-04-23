<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * $I('foo')
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends TraversalExpression
{
    /**
     * @var Expression[]
     */
    private $argumentExpressions;
    
    public function __construct(Expression $valueExpression, array $argumentExpressions)
    {
        parent::__construct($valueExpression);
        $this->argumentExpressions = $argumentExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function getArgumentExpressions()
    {
        return $this->argumentExpressions;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkInvocation($this);
    }
    
    public function simplify()
    {
        $valueExpression = $this->valueExpression->simplify();
        $argumentExpressions = self::simplifyAll($this->argumentExpressions);
        
        if ($valueExpression instanceof ValueExpression && self::allOfType($argumentExpressions, ValueExpression::getType())) {
            $objectValue = $valueExpression->getValue();
            $argumentValues = [];
            
            foreach ($argumentExpressions as $argumentExpression) {
                $argumentValues[] = $argumentExpression->getValue();
            }
            
            return Expression::value(call_user_func_array($objectValue, $argumentValues));
        }
        
        return $this->update($valueExpression, $argumentExpressions);
    }
    
    /**
     * @return self
     */
    public function update(Expression $valueExpression, array $argumentExpressions)
    {
        if ($this->valueExpression === $valueExpression && $this->argumentExpressions === $argumentExpressions) {
            return $this;
        }
        
        return new self($valueExpression, $argumentExpressions);
    }
    
    protected function updateValueExpression(Expression $valueExpression)
    {
        return new self($valueExpression, $this->argumentExpressions);
    }
    
    protected function compileCode(&$code)
    {
        $this->valueExpression->compileCode($code);
        $code .= '(';
        $code .= implode(',', self::compileAll($this->argumentExpressions));
        $code .= ')';
    }
    
    public function dataToSerialize()
    {
        return $this->argumentExpressions;
    }
    
    public function unserializedData($data)
    {
        $this->argumentExpressions = $data;
    }
    
    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
        $this->argumentExpressions = self::cloneAll($this->argumentExpressions);
    }
}