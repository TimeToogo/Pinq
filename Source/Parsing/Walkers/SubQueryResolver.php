<?php

namespace Pinq\Parsing\Walkers;

use \Pinq\Expressions as O;

/**
 * Resolves all method call expression to their equivalent query
 * expression. 
 * TODO: Finish and implement
 */
class SubQueryResolver extends O\ExpressionWalker
{
    /**
     * @var \Pinq\Providers\IQueryProvider
     */
    private $QueryProvider;

    private $QueryVariableNames;

    public function __construct(\Pinq\Providers\IQueryProvider $QueryProvider, array $QueryVariableNames)
    {
        $this->QueryProvider = $QueryProvider;
        $this->QueryVariableNames = $QueryVariableNames;
    }

    public function WalkMethodCall(O\MethodCallExpression $Expression)
    {
        if($Expression->OriginatesFrom(O\VariableExpression::GetType())
                && $Expression->GetOriginExpression()->GetNameExpression() instanceof O\ValueExpression
                && in_array($Expression->GetOriginExpression()->GetNameExpression()->GetValue(), $this->QueryVariableNames, true)) {
            $QueryStreamExpression = $this->ResolveQueryStream($Expression);

            if ($QueryStreamExpression !== null) {
                return $QueryStreamExpression;
            } 
            else {
                return parent::WalkMethodCall($Expression);
            }
        }
    }

    private function ResolveQueryStream(O\MethodCallExpression $Expression)
    {
        $Queryable = $this->QueryProvider->CreateQueryable(new \Pinq\Queries\Scope([]));
        
        $MethodCallExpressions = [];
        while ($Expression instanceof O\MethodCallExpression) {
            $MethodCallExpressions[] = $Expression;
            if (!($Expression->GetNameExpression() instanceof O\ValueExpression)) {
                return null;
            }
            if (!method_exists($Queryable, $Expression->GetNameExpression()->GetValue())) {
                return null;
            }

            $Expression = $Expression->GetValueExpression();
        }

        $QueryableExpression = O\Expression::Value($Queryable);

        foreach ($MethodCallExpressions as $Expression) {
            $Expression = $this->ResolveClosureArguments($Expression);
            $Expression = $Expression->UpdateValue($QueryableExpression);
            
            $QueryableExpression = $Expression->Simplify();
            if (!($QueryableExpression instanceof O\ValueExpression)) {
                return null;
            }
        }
        $ResolvedQueryable = $QueryableExpression->GetValue();
        
        return O\Expression::SubQuery($Expression->GetOriginExpression(), $ResolvedQueryable->GetScope());
    }

    private function ResolveClosureArguments(O\MethodCallExpression $Expression)
    {
        $ArgumentExpressions = $Expression->GetArgumentExpressions();

        foreach ($ArgumentExpressions as $Key => $ArgumentExpression) {
            if ($ArgumentExpression instanceof O\ClosureExpression) {
                $FunctionExpressionTree = \Pinq\FunctionExpressionTree::FromClosureExpression($ArgumentExpression);
                $ArgumentExpressions[$Key] = O\Expression::Value($FunctionExpressionTree);
            }
        }

        return $Expression->Update(
                $Expression->GetValueExpression(),
                $Expression->GetNameExpression(),
                $ArgumentExpressions);
    }
}
