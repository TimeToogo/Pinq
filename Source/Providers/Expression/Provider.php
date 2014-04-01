<?php

namespace Pinq\Providers\Expression;

use \Pinq\Parsing\IFunctionToExpressionTreeConverter;
use \Pinq\Parsing\FunctionExpressionTree;
use \Pinq\Queries;

abstract class Provider extends \Pinq\Providers\Base\Provider implements \Pinq\IQueryProvider
{
    /**
     * @var IFunctionToExpressionTreeConverter
     */
    protected $FunctionConverter;

    public function __construct(IFunctionToExpressionTreeConverter $FunctionConverter)
    {
        $this->FunctionConverter = $FunctionConverter;
    }

    final public function InstantiateQueryBuilder()
    {
        return new Queries\LazyBuilder(new Queries\Expression\Builder($this->FunctionConverter));
    }
    
    private function GetExpressionTree(callable $Function = null)
    {
        if($Function === null) {
            return null;
        }
        return $this->FunctionConverter->Convert($Function);
    }

    final public function Aggregate(callable $Function)
    {
        return $this->AggregateExpression($this->GetExpressionTree($Function));
    }
    protected abstract function AggregateExpression(FunctionExpressionTree $ExpressionTree);

    final public function All(callable $Function = null)
    {
        return $this->AllExpression($this->GetExpressionTree($Function));
    }
    protected abstract function AllExpression(FunctionExpressionTree $ExpressionTree = null);

    final public function Any(callable $Function = null)
    {
        return $this->AnyExpression($this->GetExpressionTree($Function));
    }
    protected abstract function AnyExpression(FunctionExpressionTree $ExpressionTree = null);

    final public function Average(callable $Function = null)
    {
        return $this->AverageExpression($this->GetExpressionTree($Function));
    }
    protected abstract function AverageExpression(FunctionExpressionTree $ExpressionTree = null);

    final public function Implode($Delimiter, callable $Function = null)
    {
        return $this->ImplodeExpression($Delimiter, $this->GetExpressionTree($Function));
    }
    protected abstract function ImplodeExpression($Delimiter, FunctionExpressionTree $ExpressionTree = null);

    final public function Maximum(callable $Function = null)
    {
        return $this->MaximumExpression($this->GetExpressionTree($Function));
    }
    protected abstract function MaximumExpression(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Minimum(callable $Function = null)
    {
        return $this->MinimumExpression($this->GetExpressionTree($Function));
    }
    protected abstract function MinimumExpression(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Sum(callable $Function = null)
    {
        return $this->SumExpression($this->GetExpressionTree($Function));
    }
    protected abstract function SumExpression(FunctionExpressionTree $ExpressionTree = null);
}
