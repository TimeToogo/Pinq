<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * $I->Method($One, true)
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class MethodCallExpression extends ObjectOperationExpression
{
    /**
     * @var Expression
     */
    private $nameExpression;
    
    /**
     * @var Expression[]
     */
    private $argumentExpressions;
    
    public function __construct(Expression $objectValueExpression, Expression $nameExpression, array $argumentExpressions = [])
    {
        parent::__construct($objectValueExpression);
        $this->nameExpression = $nameExpression;
        $this->argumentExpressions = $argumentExpressions;
    }
    
    /**
     * @return Expression
     */
    public function getNameExpression()
    {
        return $this->nameExpression;
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
        return $walker->walkMethodCall($this);
    }
    
    public function simplify()
    {
        $valueExpression = $this->valueExpression->simplify();
        $nameExpression = $this->nameExpression->simplify();
        $argumentExpressions = self::simplifyAll($this->argumentExpressions);
        
        if ($valueExpression instanceof ValueExpression && $nameExpression instanceof ValueExpression && self::allOfType($argumentExpressions, ValueExpression::getType())) {
            $objectValue = $valueExpression->getValue();
            $name = $nameExpression->getValue();
            $argumentValues = [];
            
            foreach ($argumentExpressions as $argumentExpression) {
                $argumentValues[] = $argumentExpression->getValue();
            }
            
            return Expression::value(call_user_func_array([$objectValue, $name], $argumentValues));
        }
        
        return $this->update(
                $valueExpression,
                $this->nameExpression,
                $argumentExpressions);
    }
    
    /**
     * @return self
     */
    public function update(Expression $objectValueExpression, Expression $nameExpression, array $argumentExpressions)
    {
        if ($this->valueExpression === $objectValueExpression && $this->nameExpression === $nameExpression && $this->argumentExpressions === $argumentExpressions) {
            return $this;
        }
        
        return new self(
                $objectValueExpression,
                $nameExpression,
                $argumentExpressions);
    }
    
    protected function updateValueExpression(Expression $valueExpression)
    {
        return new self(
                $valueExpression,
                $this->nameExpression,
                $this->argumentExpressions);
    }
    
    protected function compileCode(&$code)
    {
        $this->valueExpression->compileCode($code);
        $code .= '->';
        
        if ($this->nameExpression instanceof ValueExpression && \Pinq\Utilities::isNormalSyntaxName($this->nameExpression->getValue())) {
            $code .= $this->nameExpression->getValue();
        }
        else {
            $code .= '{';
            $this->nameExpression->compileCode($code);
            $code .= '}';
        }
        
        $code .= '(';
        $code .= implode(',', self::compileAll($this->argumentExpressions));
        $code .= ')';
    }
    
    public function dataToSerialize()
    {
        return [$this->nameExpression, $this->argumentExpressions];
    }
    
    public function unserializedData($data)
    {
        list($this->nameExpression, $this->argumentExpressions) = $data;
    }
    
    public function __clone()
    {
        $this->valueExpression = clone $this->valueExpression;
        $this->nameExpression = clone $this->nameExpression;
        $this->argumentExpressions = self::cloneAll($this->argumentExpressions);
    }
}