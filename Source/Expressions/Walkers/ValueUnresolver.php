<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Expressions as O;

/**
 * Unresolves and stores any values in the expression tree and replaces them with uncolliding variable names
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
     * @var int
     */
    private $VariableCount = 0;
    
    /**
     * @var array<string, mixed>
     */
    private $VariableNameValueMap = [];
    
    /**
     * @var callable| null
     */
    private $ValueFilterFunction;
    
    public function __construct(callable $ValueFilterFunction = null)
    {
        $this->ValueFilterFunction = $ValueFilterFunction;
    }

    /**
     * @return array<string, mixed>
     */
    public function GetVariableNameValueMap()
    {
        return $this->VariableNameValueMap;
    }

    public function ResetValueExpressionVariableNameMap()
    {
        $this->VariableCount = 0;
        $this->ValueExpressionVariableNameMap = [];
    }

    public function SetValueFilter(callable $ValueFilterFunction)
    {
        $this->ValueFilterFunction = $ValueFilterFunction;
    }
    
    private function MakeVariableName() 
    {
        return '___' . ++$this->VariableCount . '___';
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
