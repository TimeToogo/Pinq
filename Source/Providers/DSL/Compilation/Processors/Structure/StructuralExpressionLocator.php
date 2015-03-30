<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ParameterCollection;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

/**
 * Implementation of the structural expression locator.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionLocator extends StructuralExpressionQueryProcessor
{
    /**
     * @var ParameterCollection
     */
    protected $parameters;

    public function __construct(
            ParameterCollection $expressionCollection,
            IStructuralExpressionProcessor $processor
    ) {
        parent::__construct($expressionCollection, $processor);
    }

    /**
     * @param ParameterCollection            $parameters
     * @param Queries\IQuery                 $query
     * @param IStructuralExpressionProcessor $processor
     *
     * @return void
     */
    public static function processQuery(ParameterCollection $parameters, Queries\IQuery $query, IStructuralExpressionProcessor $processor)
    {
        $processor = Expression\ProcessorFactory::from(
                $query,
                new self($parameters, $processor)
        );
        $processor->buildQuery();
    }

    public function processFunction(IFunction $function)
    {
        $expressionParameterizer = new StructuralExpressionWalker(
                function (
                        IStructuralExpressionProcessor $processor,
                        O\Expression $expression
                ) use ($function) {
                    $processor->parameterize($function, $expression, $this->parameters);

                    return $expression;
                },
                $function,
                $this->processor);

        return $function->walk($expressionParameterizer);
    }
}
