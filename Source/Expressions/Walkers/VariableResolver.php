<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Expressions as O;

/**
 * Resolves variables within the expression tree to the supplied expressions/values.
 *
 * $Var = 4 + 5 - $Unresolvable;
 * === with ['Unresolvable' => 97] resolves to ===
 * $Var = 4 + 5 - 97;
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableResolver extends O\ExpressionWalker
{
    /**
     * The array containing the variable name and the value to expression
     * to replace it with
     * 
     * @var array<string, O\Expression>
     */
    private $VariableExpressionMap = [];

    public function __construct(array $VariableExpressionMap = [])
    {
        $this->VariableExpressionMap = $VariableExpressionMap;
    }
    
    /**
     * Sets which variables to replace with what
     * 
     * @param array<string, O\Expression> $VariableExpressionMap
     */
    public function SetVariableExpressionMap(array $VariableExpressionMap)
    {
        $this->VariableExpressionMap = $VariableExpressionMap;
    }

    /*
     * Resolves scoped the variables in closures
     */
    public function WalkClosure(O\ClosureExpression $Expression)
    {
        $OriginalVariableExpressionMap = $this->VariableExpressionMap;
        
        $UsedVariableNames = $Expression->GetUsedVariableNames();
        
        //Filter to only used values
        $this->VariableExpressionMap = array_intersect_key(
                $this->VariableExpressionMap,
                array_flip(array_values($UsedVariableNames)) + ['this' => null]);//Include $this variable scope
        
        $Expression = $Expression->Update(
                $Expression->GetParameterExpressions(),
                array_diff($UsedVariableNames, array_keys($this->VariableExpressionMap)),//Remove resolved used variables
                $this->WalkAll($Expression->GetBodyExpressions()));

        //Restore parent scope with all variables values
        $this->VariableExpressionMap = $OriginalVariableExpressionMap;
        
        return $Expression;
    }

    /*
     * Replace the variable with the value expression of the current scope
     */
    public function WalkVariable(O\VariableExpression $Expression)
    {
        $NameExpression = $this->Walk($Expression->GetNameExpression())->Simplify();
        if ($NameExpression instanceof O\ValueExpression) {
            $Name = $NameExpression->GetValue();
            
            if (isset($this->VariableExpressionMap[$Name])) {
                return $this->VariableExpressionMap[$Name];
            }
        }

        return $Expression;
    }
}
