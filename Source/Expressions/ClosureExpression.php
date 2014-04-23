<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * function ($I) { return $I + 5; }
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ClosureExpression extends Expression
{
    /**
     * @var ParameterExpression[] 
     */
    private $parameterExpressions;
    
    /**
     * @var string[] 
     */
    private $usedVariables;
    
    /**
     * @var Expression[] 
     */
    private $bodyExpressions;
    
    public function __construct(array $parameterExpressions, array $usedVariables, array $bodyExpressions)
    {
        if (in_array(null, $parameterExpressions)) {
            throw new \Exception();
        }
        
        $this->parameterExpressions = $parameterExpressions;
        $this->usedVariables = $usedVariables;
        $this->bodyExpressions = $bodyExpressions;
    }
    
    /**
     * @return ParameterExpression[]
     */
    public function getParameterExpressions()
    {
        return $this->parameterExpressions;
    }
    
    /**
     * @return string[]
     */
    public function getUsedVariableNames()
    {
        return $this->usedVariables;
    }
    
    /**
     * @return Expression[]
     */
    public function getBodyExpressions()
    {
        return $this->bodyExpressions;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkClosure($this);
    }
    
    public function simplify()
    {
        return $this->update(
                self::simplifyAll($this->parameterExpressions),
                $this->usedVariables,
                self::simplifyAll($this->bodyExpressions));
    }
    
    public function update(array $parameterExpressions, array $usedVariables, array $bodyExpressions)
    {
        if ($this->parameterExpressions === $parameterExpressions && $this->usedVariables === $usedVariables && $this->bodyExpressions === $bodyExpressions) {
            return $this;
        }
        
        return new self($parameterExpressions, $usedVariables, $bodyExpressions);
    }
    
    protected function compileCode(&$code)
    {
        $code .= 'function (';
        
        if (!empty($this->parameterExpressions)) {
            $code .= implode(',', self::compileAll($this->parameterExpressions));
        }
        
        $code .= ')';
        
        if (!empty($this->usedVariables)) {
            $code .= 'use (';
            $code .= '$' . implode(', $', $this->usedVariables);
            $code .= ')';
        }
        
        $code .= '{';
        
        foreach ($this->bodyExpressions as $expression) {
            $expression->compileCode($code);
            $code .= ';';
        }
        
        $code .= '}';
    }
    
    public function serialize()
    {
        return serialize([$this->parameterExpressions, $this->usedVariables, $this->bodyExpressions]);
    }
    
    public function unserialize($serialized)
    {
        list($this->parameterExpressions, $this->usedVariables, $this->bodyExpressions) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->parameterExpressions = self::cloneAll($this->parameterExpressions);
        $this->bodyExpressions = self::cloneAll($this->bodyExpressions);
    }
}