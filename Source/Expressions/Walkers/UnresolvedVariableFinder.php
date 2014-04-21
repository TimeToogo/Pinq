<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Expressions as O;

/**
 * Locates and stores all unresolved variables
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnresolvedVariableFinder extends O\ExpressionWalker
{
    /**
     * @var string[] 
     */
    private $VariablesToIgnore = [];
    
    /**
     * @var string[] 
     */
    private $UnresolvedVariables = [];
    
    /**
     * @return void
     */
    public function ResetUnresolvedVariables()
    {
        $this->UnresolvedVariables = [];
    }

    /**
     * The unresolved variables
     * 
     * @return string[]
     */
    public function GetUnresolvedVariables()
    {
        return $this->UnresolvedVariables;
    }
    
    public function WalkAssignment(O\AssignmentExpression $Expression)
    {
        $AssignToExpression = $Expression->GetAssignToExpression();
        
        //Ignore variable if making assignment
        if($AssignToExpression instanceof O\VariableExpression 
                && in_array($Expression->GetOperator(), [O\Operators\Assignment::Equal, O\Operators\Assignment::EqualReference])) {
            $this->Walk($AssignToExpression->GetNameExpression());
        }
        else {
            $this->Walk($AssignToExpression);
        }
        
        $AssignmentValueExpression = $Expression->GetAssignmentValueExpression();
        $this->Walk($AssignmentValueExpression);
        
        return $Expression;
    }
    
    public function WalkClosure(O\ClosureExpression $Expression)
    {
        foreach($Expression->GetUsedVariableNames() as $UsedVariableName) {
            $this->WalkVariable(O\Expression::Variable(O\Expression::Value($UsedVariableName)));
        }
        
        $OriginalVariablesToIgnore = $this->VariablesToIgnore;
        $this->VariablesToIgnore = array_map(function ($I) { return $I->GetName(); }, $Expression->GetParameterExpressions());
        
        $this->WalkAll($Expression->GetBodyExpressions());
        
        $this->VariablesToIgnore = $OriginalVariablesToIgnore;
        
        return $Expression;
    }
    
    public function WalkVariable(O\VariableExpression $Expression)
    {
        $NameExpression = $Expression->GetNameExpression();
        $VariableName = $NameExpression instanceof O\ValueExpression ? $NameExpression->GetValue() : $NameExpression->Compile();
        $this->AddUnresolvedVariable($VariableName);
        
        return $Expression;
    }
    
    private function AddUnresolvedVariable($VariableName)
    {
        if (!in_array($VariableName, $this->UnresolvedVariables) && !in_array($VariableName, $this->VariablesToIgnore)) {
            $this->UnresolvedVariables[] = $VariableName;
        }
    }
}
