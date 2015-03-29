<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ResolvedParameterRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

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
            IStructuralExpressionProcessor $processor
    ) {
        parent::__construct($parameters, $processor);
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
                new self($parameters, $processor)
        );

        return $processor->buildQuery();
    }

    public function processFunction(IFunction $function)
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
