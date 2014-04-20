<?php

namespace Pinq\Expressions;

class ClosureExpression extends Expression
{
    private $ParameterExpressions;
    private $UsedVariables;
    private $BodyExpressions;

    public function __construct(array $ParameterExpressions, array $UsedVariables, array $BodyExpressions)
    {
        if(in_array(null, $ParameterExpressions)) {
            throw new \Exception();
        }
        $this->ParameterExpressions = $ParameterExpressions;
        $this->UsedVariables = $UsedVariables;
        $this->BodyExpressions = $BodyExpressions;
    }
    
    /**
     * @return ParameterExpression[]
     */
    public function GetParameterExpressions()
    {
        return $this->ParameterExpressions;
    }

    public function GetUsedVariableNames()
    {
        return $this->UsedVariables;
    }

    /**
     * @return Expression[]
     */
    public function GetBodyExpressions()
    {
        return $this->BodyExpressions;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkClosure($this);
    }

    public function Simplify()
    {
        return $this->Update(
                self::SimplifyAll($this->ParameterExpressions),
                $this->UsedVariables,
                self::SimplifyAll($this->BodyExpressions));
    }

    public function Update(array $ParameterExpressions, array $UsedVariables, array $BodyExpressions)
    {
        if ($this->ParameterExpressions === $ParameterExpressions
                && $this->UsedVariables === $UsedVariables
                && $this->BodyExpressions === $BodyExpressions) {
            return $this;
        }
        
        return new self($ParameterExpressions, $UsedVariables, $BodyExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'function (';
        
        if (!empty($this->ParameterExpressions)) {
            $Code .= implode(',', self::CompileAll($this->ParameterExpressions));
        }
        
        $Code .= ')';
        
        if (!empty($this->UsedVariables)) {
            $Code .= 'use (';
            $Code .= '$' . implode(', $', $this->UsedVariables);
            $Code .= ')';
        }
        
        $Code .= '{';
        foreach ($this->BodyExpressions as $Expression) {
            $Expression->CompileCode($Code);
            $Code .= ';';
        }
        $Code .= '}';
    }
    
    public function serialize()
    {
        return serialize([$this->ParameterExpressions, $this->UsedVariables, $this->BodyExpressions]);
    }
    
    public function unserialize($Serialized)
    {
        list($this->ParameterExpressions, $this->UsedVariables, $this->BodyExpressions) = unserialize($Serialized);
    }
    
    public function __clone()
    {
        $this->ParameterExpressions = self::CloneAll($this->ParameterExpressions);
        $this->BodyExpressions = self::CloneAll($this->BodyExpressions);
    }
}
