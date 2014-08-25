<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ResolvedExpressionRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries\Functions\FunctionBase;
use Pinq\Queries;

/**
 * Implementation of the structural expression inliner.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionInliner extends StructuralExpressionProcessor
{
    /**
     * @var ResolvedExpressionRegistry
     */
    protected $expressions;

    public function __construct(
            ResolvedExpressionRegistry $expressionRegistry,
            IStructuralExpressionProcessor $processor,
            Queries\IScope $scope
    ) {
        parent::__construct($expressionRegistry, $processor, $scope);
    }

    /**
     * @param Queries\IQuery                 $query
     * @param IStructuralExpressionProcessor $processor
     * @param ResolvedExpressionRegistry     $expressionRegistry
     *
     * @return Queries\IOperationQuery|Queries\IRequestQuery
     */
    public static function processQuery(
            Queries\IQuery $query,
            IStructuralExpressionProcessor $processor,
            ResolvedExpressionRegistry $expressionRegistry
    ) {
        $processor = Expression\ProcessorFactory::from(
                $query,
                new self($expressionRegistry, $processor, $query->getScope())
        );
        return $processor->buildQuery();
    }

    public function forSubScope(Queries\IScope $scope)
    {
        return new static($this->expressions, $this->processor, $scope);
    }

    public function processFunction(FunctionBase $function)
    {
        $expressionInliner = new StructuralExpressionWalker(
                function (
                        IStructuralExpressionProcessor $processor,
                        O\Expression $expression
                ) {
                    return $processor->inline($expression, $this->expressions);
                },
                $function,
                $this->processor);

        return $function->walk($expressionInliner);
    }
}