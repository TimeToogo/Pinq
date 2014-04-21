<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Expressions as O;

/**
 * Unresolves and stores any values in the expression tree and replaces them with uncolliding variable names.
 * This is useful when compiling an expression tree and certain values shouldn't be stored inline, such as
 * objects or arrays.
 *
 * 3 + $Var;
 * === will become ===
 * ${'some unclashing name'} + $Var
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ValueUnresolver extends O\ExpressionWalker
{
    /**
     * The current amount of variables that have be inserted
     * 
     * @var int
     */
    private $VariableCount = 0;
    
    /**
     * An array with the variable names as key and the
     * respective value as the value
     * 
     * @var array<string, mixed>
     */
    private $VariableNameValueMap = [];
    
    /**
     * Determines whether the value should be replaced a variable
     * 
     * @var callable| null
     */
    private $ValueFilterFunction;
    
    public function __construct(callable $ValueFilterFunction = null)
    {
        $this->ValueFilterFunction = $ValueFilterFunction;
    }

    /**
     * Returns the variable value map
     * 
     * @return array<string, mixed>
     */
    public function GetVariableNameValueMap()
    {
        return $this->VariableNameValueMap;
    }
    
    /**
     * @return void
     */
    public function ResetVariableNameValueMap()
    {
        $this->VariableCount = 0;
        $this->VariableNameValueMap = [];
    }
    
    /**
     * Sets the function to filter which values should not be inlined
     * 
     * @param callable $ValueFilterFunction The filter function
     */
    public function SetValueFilter(callable $ValueFilterFunction)
    {
        $this->ValueFilterFunction = $ValueFilterFunction;
    }
    
    private function MakeVariableName() 
    {
        return '___' . ++$this->VariableCount . '___';
    }
    
    /**
     * Walks the and finds any values in the body then adds them as used variables
     * 
     * @param O\ClosureExpression $Expression
     * @return O\ClosureExpression
     */
    public function WalkClosure(O\ClosureExpression $Expression)
    {
        $WalkedBodyExpressions = $this->WalkAll($Expression->GetBodyExpressions());
        
        return $Expression->Update(
                $Expression->GetParameterExpressions(),
                array_merge($Expression->GetUsedVariableNames(), array_keys($this->VariableNameValueMap)),
                $WalkedBodyExpressions);
    }
    
    public function WalkValue(O\ValueExpression $Expression)
    {
        $ValueFilter = $this->ValueFilterFunction;
        $Value = $Expression->GetValue();
        
        if($ValueFilter === null || $ValueFilter($Value)) {
            $VariableName = array_search($Value, $this->VariableNameValueMap, true);

            if($VariableName === false) {
                $VariableName = $this->MakeVariableName();
                $this->VariableNameValueMap[$VariableName] = $Value;
            }

            return O\Expression::Variable(O\Expression::Value($VariableName)); 
        }
        
        return $Expression;
    }
}
