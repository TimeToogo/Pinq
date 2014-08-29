<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ResolvedParameterRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Implementation of the structural expression inliner.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionInliner extends StructuralExpressionQueryProcessor
{
    /**
     * @var ResolvedParameterRegistry
     */
    protected $parameters;

    public function __construct(
            ResolvedParameterRegistry $parameters,
            IStructuralExpressionProcessor $processor,
            Queries\IScope $scope
    ) {
        parent::__construct($parameters, $processor, $scope);
    }

    /**
     * @param ResolvedParameterRegistry      $parameters
     * @param Queries\IQuery                 $query
     * @param IStructuralExpressionProcessor $processor
     *
     * @return Queries\IOperationQuery|Queries\IRequestQuery
     */
    public static function processQuery(
            ResolvedParameterRegistry $parameters,
            Queries\IQuery $query,
            IStructuralExpressionProcessor $processor
    ) {
        $processor = Expression\ProcessorFactory::from(
                $query,
                new self($parameters, $processor, $query->getScope())
        );

        return $processor->buildQuery();
    }

    public function forSubScope(Queries\IScope $scope)
    {
        return new static($this->parameters, $this->processor, $scope);
    }

    public function processFunction(FunctionBase $function)
    {
        $expressionInliner = new StructuralExpressionWalker(
                function (
                        IStructuralExpressionProcessor $processor,
                        O\Expression $expression
                ) use ($function) {
                    return $processor->inline($function, $expression, $this->parameters);
                },
                $function,
                $this->processor);

        return $function->walk($expressionInliner);
    }
}