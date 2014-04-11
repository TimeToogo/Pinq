<?php

namespace Pinq\Parsing\Walkers;

use \Pinq\Expressions as O;

/**
 * Resolves variables within the expression tree.
 *
 * {
 *     $Var = 4 + 5 - $Unresolvable;
 *     return 3 + $Var;
 * }
 * === with ['Unresolvable' => 97] resolves to ===
 * {
 *     $Var = 4 + 5 - 97;
 *     return 3 + $Var
 * }
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableResolver extends O\ExpressionWalker
{
    private $ScopeUnresolvedVariablesStack = [];
    private $UnresolvedVariables = [];
    private $ScopeVariableExpressionMapStack = [];
    private $VariableExpressionMap = [];

    public function __construct(array $VariableExpressionMap = [])
    {
        $this->VariableExpressionMap = $VariableExpressionMap;
    }

    public function HasUnresolvedVariables()
    {
        return count($this->UnresolvedVariables) > 0;
    }

    public function GetUnresolvedVariables()
    {
        return $this->UnresolvedVariables;
    }

    public function ResetUnresolvedVariables()
    {
        $this->UnresolvedVariables = [];
    }

    public function SetVariableExpressionMap(array $VariableExpressionMap)
    {
        $this->VariableExpressionMap = $VariableExpressionMap;
    }

    private function Scope(array $UsedVariableNames)
    {
        array_push($this->ScopeUnresolvedVariablesStack, $this->UnresolvedVariables);
        $this->UnresolvedVariables = [];

        array_push($this->ScopeVariableExpressionMapStack, $this->VariableExpressionMap);
        $this->VariableExpressionMap = array_intersect_key(
                $this->VariableExpressionMap,
                array_flip(array_values($UsedVariableNames)));

    }
    private function Unscope(array $ParameterNames)
    {
        $this->UnresolvedVariables = array_merge(
                array_pop($this->ScopeUnresolvedVariablesStack),
                array_diff($this->UnresolvedVariables, $ParameterNames));

        $this->VariableExpressionMap = array_pop($this->ScopeVariableExpressionMapStack);
    }

    /*
     * Resolves scoped the variables in closures
     */
    public function WalkClosure(O\ClosureExpression $Expression)
    {
        $UsedVariableNames = $Expression->GetUsedVariableNames();

        $this->Scope($UsedVariableNames);

        $Expression = $Expression->Update(
                $Expression->GetParameterExpressions(),
                $UsedVariableNames,
                $this->WalkAll($Expression->GetBodyExpressions()));

        $this->Unscope($UsedVariableNames);

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
        $this->AddUnresolvedVariable($Expression->Compile());

        return $Expression;
    }

    private function AddUnresolvedVariable($Name)
    {
        if (!in_array($Name, $this->UnresolvedVariables)) {
            $this->UnresolvedVariables[] = $Name;
        }
    }
}
