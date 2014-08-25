<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionCollection;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Implementation of the structural expression locator.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionLocator extends StructuralExpressionProcessor
{
    /**
     * @var ExpressionCollection
     */
    protected $expressions;

    public function __construct(
            ExpressionCollection $expressionCollection,
            IStructuralExpressionProcessor $processor,
            Queries\IScope $scope
    ) {
        parent::__construct($expressionCollection, $processor, $scope);
    }

    /**
     * @param Queries\IQuery                 $query
     * @param IStructuralExpressionProcessor $processor
     *
     * @return ExpressionRegistry
     */
    public static function processQuery(Queries\IQuery $query, IStructuralExpressionProcessor $processor)
    {
        $expressionCollection = new ExpressionCollection();
        $processor            = Expression\ProcessorFactory::from(
                $query,
                new self($expressionCollection, $processor, $query->getScope())
        );
        $processor->buildQuery();

        return $expressionCollection->buildRegistry();
    }

    public function forSubScope(Queries\IScope $scope)
    {
        return new static($this->expressions, $this->processor, $scope);
    }

    public function processFunction(FunctionBase $function)
    {
        $expressionContext       = $this->expressions->forFunction($function);
        $expressionParameterizer = new StructuralExpressionWalker(
                function (
                        IStructuralExpressionProcessor $processor,
                        O\Expression $expression
                ) use ($expressionContext) {
                    $processor->parameterize($expression, $expressionContext);
                    return $expression;
                },
                $function,
                $this->processor);

        return $function->walk($expressionParameterizer);
    }
}