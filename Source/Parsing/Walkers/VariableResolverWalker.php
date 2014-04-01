<?php

namespace Pinq\Parsing\Walkers;

use \Pinq\Expressions as O;
use \Pinq\Expressions\Operators;

/**
 * Resolves all resolvable variables within the expression tree.
 *
 * {
 *     $Var = 4 + 5 - $Unresolvable;
 *     return 3 + $Var;
 * }
 * === resolves to ===
 * {
 *     4 + 5 - $Unresolvable;
 *     return 3 + (4 + 5 - $Unresolvable)
 * }
 * === with ['Unresolvable' => 97] it resolves to ===
 * {
 *     4 + 5 - 97;
 *     return 3 + (4 + 5 - 97)
 * }
 *
 * This can handle assignments, variable variables and closures
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableResolverWalker extends O\ExpressionWalker
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

    public function SetVariableResolutionMap(array $VariableExpressionMap)
    {
        $this->VariableExpressionMap = $VariableExpressionMap;
    }

    private static $AssignmentToBinaryOperator = [
        Operators\Assignment::Addition => Operators\Binary::Addition,
        Operators\Assignment::BitwiseAnd => Operators\Binary::BitwiseAnd,
        Operators\Assignment::BitwiseOr => Operators\Binary::BitwiseOr,
        Operators\Assignment::BitwiseXor => Operators\Binary::BitwiseXor,
        Operators\Assignment::Concatenate => Operators\Binary::Concatenation,
        Operators\Assignment::Division => Operators\Binary::Division,
        Operators\Assignment::Modulus => Operators\Binary::Modulus,
        Operators\Assignment::Multiplication => Operators\Binary::Multiplication,
        Operators\Assignment::ShiftLeft => Operators\Binary::ShiftLeft,
        Operators\Assignment::ShiftRight => Operators\Binary::ShiftRight,
        Operators\Assignment::Subtraction => Operators\Binary::Subtraction,
    ];
    private function AssignmentToBinaryOperator($AssignmentOperator)
    {
        return isset(self::$AssignmentToBinaryOperator[$AssignmentOperator]) ?
                self::$AssignmentToBinaryOperator[$AssignmentOperator] : null;
    }

    /*
     * Convert any assignments to the equivalent binary expression and stores resolved value.
     */
    public function WalkAssignment(O\AssignmentExpression $Expression)
    {
        $AssignToExpression = $Expression->GetAssignToExpression();
        if ($AssignToExpression instanceof O\VariableExpression) {
            $AssignToExpression = $AssignToExpression->Update(
                    $this->Walk($AssignToExpression->GetNameExpression()));
        } 
        else {
            $AssignToExpression = $this->Walk($AssignToExpression);
        }
        $AssignToExpression = $AssignToExpression->Simplify();

        $AssignmentOperator = $Expression->GetOperator();
        $AssignmentValueExpression = $this->Walk($Expression->GetAssignmentValueExpression());

        if($AssignToExpression instanceof O\VariableExpression
                && $AssignToExpression->GetNameExpression() instanceof O\ValueExpression) {
            $AssignmentName = $AssignToExpression->GetNameExpression()->GetValue();
            $BinaryOperator = $this->AssignmentToBinaryOperator($AssignmentOperator);

            if ($BinaryOperator !== null) {
                $CurrentValueExpression = isset($this->VariableExpressionMap[$AssignmentName]) ?
                        $this->VariableExpressionMap[$AssignmentName] : O\Expression::Value(null);

                $VariableValueExpression =
                        O\Expression::BinaryOperation(
                                $CurrentValueExpression,
                                $BinaryOperator,
                                $AssignmentValueExpression);

            } 
            else {
                $VariableValueExpression = $AssignmentValueExpression;
            }

            $this->VariableExpressionMap[$AssignmentName] = $VariableValueExpression;

            return $VariableValueExpression;
        }

        return $Expression->Update(
                $AssignToExpression,
                $AssignmentOperator,
                $AssignmentValueExpression);
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
                $Expression->GetParameterNameTypeHintMap(),
                $UsedVariableNames,
                $this->WalkAll($Expression->GetBodyExpressions()));

        $this->Unscope($ExGetParameterNameTypeHintMapmeterNames());

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
            $this->AddUnresolvedVariable($Name);
        } 
        else {
            $this->AddUnresolvedVariable($this->GetUnresolvedName($NameExpression));
        }

        return $Expression;
    }

    private function AddUnresolvedVariable($Name)
    {
        if (!in_array($Name, $this->UnresolvedVariables)) {
            $this->UnresolvedVariables[] = $Name;
        }
    }

    private function GetUnresolvedName(O\Expression $NameExpression)
    {
        if ($NameExpression instanceof O\ValueExpression) {
            return $NameExpression->GetValue();
        } 
        else if ($NameExpression instanceof O\VariableExpression) {
            return '$' . $this->GetUnresolvedName($NameExpression);
        } 
        else {
            return '{COMPLEX UNRESOLVED NAME}';
        }
    }
}
