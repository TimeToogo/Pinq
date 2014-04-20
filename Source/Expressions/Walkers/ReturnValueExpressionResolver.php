<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Expressions as O;
use \Pinq\Expressions\Operators;

/**
 * Resolves and stores the expression of the return statements in an expression tree.
 *
 * $Var = 4 + 5 - $Unresolvable;
 * return 3 + $Var;
 * === will be resolved to ===
 * 3 + (4 + 5 - $Unresolvable)
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReturnValueExpressionResolver extends O\ExpressionWalker
{
    /**
     * @var array<string, O\Expression>
     */
    private $VariableExpressionMap = [];
    
    /**
     * @var VariableResolver
     */
    private $VariableResolver;
    
    /**
     * @var O\Expression[]
     */
    private $ReturnValueExpressions = [];
    
    public function __construct()
    {
        $this->VariableResolver = new VariableResolver();
    }
    
    /**
     * @return O\Expression[]
     */
    public function GetResolvedReturnValueExpression()
    {
        return $this->ReturnValueExpressions;
    }

    public function ResetReturnExpressions()
    {
        $this->VariableExpressionMap = [];
        $this->ReturnValueExpressions = [];
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

    /**
     * @param string $AssignmentOperator
     */
    private function AssignmentToBinaryOperator($AssignmentOperator)
    {
        return isset(self::$AssignmentToBinaryOperator[$AssignmentOperator]) ?
                self::$AssignmentToBinaryOperator[$AssignmentOperator] : null;
    }
    
    private function ResolveVariables(O\Expression $Expression) 
    {
        $this->VariableResolver->SetVariableExpressionMap($this->VariableExpressionMap);
        return $this->VariableResolver->Walk($Expression);
    }

    /*
     * Convert any assignments to the equivalent binary expression and stores the value expression.
     */
    public function WalkAssignment(O\AssignmentExpression $Expression)
    {
        $AssignToExpression = $this->Walk($this->ResolveVariables($Expression->GetAssignToExpression()))->Simplify();
        $AssignmentOperator = $Expression->GetOperator();
        $AssignmentValueExpression = $this->Walk($this->ResolveVariables($Expression->GetAssignmentValueExpression()));

        if($AssignToExpression instanceof O\VariableExpression
                && $AssignToExpression->GetNameExpression() instanceof O\ValueExpression) {
            $AssignmentName = $AssignToExpression->GetNameExpression()->GetValue();
            $BinaryOperator = $this->AssignmentToBinaryOperator($AssignmentOperator);

            if ($BinaryOperator !== null) {
                $CurrentValueExpression = isset($this->VariableExpressionMap[$AssignmentName]) ?
                        $this->VariableExpressionMap[$AssignmentName] : $AssignToExpression;

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
        }
        
        return $Expression;
    }
    
    public function WalkClosure(O\ClosureExpression $Expression)
    {
        //Ignore closures
        return $Expression;
    }
    
    public function WalkReturn(O\ReturnExpression $ReturnExpression)
    {
        if($ReturnExpression->HasValueExpression()) {
            $this->ReturnValueExpressions[] = $this->ResolveVariables($ReturnExpression->GetValueExpression())->Simplify();
        }
        else {
            $this->ReturnValueExpressions[] = O\Expression::Value(null);
        }
        
        return $ReturnExpression;
    }
}
