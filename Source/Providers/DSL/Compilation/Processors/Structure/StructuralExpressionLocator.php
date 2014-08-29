<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ParameterCollection;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\FunctionBase;

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
            IStructuralExpressionProcessor $processor,
            Queries\IScope $scope
    ) {
        parent::__construct($expressionCollection, $processor, $scope);
    }

    /**
     * @param ParameterCollection $parameters
     * @param Queries\IQuery                                                 $query
     * @param IStructuralExpressionProcessor                                 $processor
     *
     * @return void
     */
    public static function processQuery(ParameterCollection $parameters, Queries\IQuery $query, IStructuralExpressionProcessor $processor)
    {
        $processor            = Expression\ProcessorFactory::from(
                $query,
                new self($parameters, $processor, $query->getScope())
        );
        $processor->buildQuery();
    }

    public function forSubScope(Queries\IScope $scope)
    {
        return new static($this->parameters, $this->processor, $scope);
    }

    public function processFunction(FunctionBase $function)
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