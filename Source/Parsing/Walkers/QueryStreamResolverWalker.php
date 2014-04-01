<?php

namespace Pinq\Parsing\Walkers;

use \Pinq\Expressions as O;

/**
 * Resolves all method call expression to their equivalent query stream
 * expression
 */
class QueryStreamResolverWalker extends O\ExpressionWalker
{
    /**
     * @var \Pinq\IQueryBuilder
     */
    private $QueryBuilder;

    private $QueryVariableNames;

    public function __construct(\Pinq\IQueryBuilder $QueryBuilder, array $QueryVariableNames)
    {
        $this->QueryBuilder = $QueryBuilder;
        $this->QueryVariableNames = $this->QueryVariableNames;
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
        $MethodCallExpressions = [];
        while ($Expression instanceof O\MethodCallExpression) {
            $MethodCallExpressions[] = $Expression;
            if (!($Expression->GetNameExpression() instanceof O\ValueExpression)) {
                return null;
            }
            if (!method_exists($this->QueryBuilder, $Expression->GetNameExpression()->GetValue())) {
                return null;
            }

            $Expression = $Expression->GetValueExpression();
        }

        $this->QueryBuilder->ClearStream();
        $QueryBuilderValueExpression = O\Expression::Value($this->QueryBuilder);

        foreach ($MethodCallExpressions as $Expression) {
            $Expression = $this->ResolveClosureArguments($Expression);
            $Expression = $Expression->UpdateValue($QueryBuilderValueExpression);

            if (!($Expression->Simplify() instanceof O\ValueExpression)) {
                return null;
            }
        }

        return O\Expression::SubQuery($Expression->GetOriginExpression(), $this->QueryBuilder->GetStream());
    }

    private function ResolveClosureArguments(O\MethodCallExpression $Expression)
    {
        $ArgumentExpressions = $Expression->GetArgumentExpressions();

        foreach ($ArgumentExpressions as $Key => $ArgumentExpression) {
            if ($ArgumentExpression instanceof O\ClosureExpression) {
                $FunctionExpressionTree = \Pinq\Parsing\FunctionExpressionTree::FromClosureExpression($ArgumentExpression);
                $ArgumentExpressions[$Key] = O\Expression::Value($FunctionExpressionTree);
            }
        }

        return $Expression->Update(
                $Expression->GetValueExpression(),
                $Expression->GetNameExpression(),
                $ArgumentExpressions);
    }
}
