<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;

/**
 * Implementation of the operation query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationQueryBuilder extends QueryBuilder implements IOperationQueryBuilder
{
    final public function parseOperation(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeParser     = $this->scopeBuilder->buildScopeParser();
        $operationParser = $this->buildOperationParser();

        $this->interpretOperationQuery(
                $expression,
                $scopeParser,
                $operationParser,
                $evaluationContext
        );

        return $this->buildOperationQuery(
                $scopeParser->getScope(),
                $operationParser->getOperation()
        );
    }

    /**
     * @param Queries\IScope     $scope
     * @param Queries\IOperation $operation
     *
     * @return Queries\IOperationQuery
     */
    protected function buildOperationQuery(
            Queries\IScope $scope,
            Queries\IOperation $operation
    ) {
        return new Queries\OperationQuery($scope, $operation);
    }

    public function resolveOperation(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeResolver     = $this->scopeBuilder->buildScopeResolver();
        $operationResolver = $this->buildOperationResolver();

        $this->interpretOperationQuery($expression, $scopeResolver, $operationResolver, $evaluationContext);

        return $this->buildResolvedQuery($scopeResolver, $operationResolver);
    }

    protected function interpretOperationQuery(
            O\Expression $expression,
            Interpretations\IScopeInterpretation $scopeInterpretation,
            Interpretations\IOperationInterpretation $operationInterpretation,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeInterpreter          = $this->scopeBuilder->buildScopeInterpreter(
                $scopeInterpretation,
                $evaluationContext
        );
        $operationQueryInterpreter = $this->buildOperationQueryInterpreter(
                $operationInterpretation,
                $scopeInterpreter,
                $evaluationContext
        );

        $operationQueryInterpreter->interpret($expression);
    }

    /**
     * @param Interpretations\IOperationInterpretation $operationInterpretation
     * @param IScopeInterpreter                        $scopeInterpreter
     * @param O\IEvaluationContext                     $evaluationContext
     *
     * @return IOperationQueryInterpreter
     */
    protected function buildOperationQueryInterpreter(
            Interpretations\IOperationInterpretation $operationInterpretation,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        return new OperationQueryInterpreter($operationInterpretation, $scopeInterpreter, $evaluationContext);
    }

    /**
     * @return Interpretations\IOperationParser
     */
    protected function buildOperationParser()
    {
        return new Interpretations\OperationParser($this->functionInterpreter);
    }

    /**
     * @return Interpretations\IOperationResolver
     */
    protected function buildOperationResolver()
    {
        return new Interpretations\OperationResolver($this->functionInterpreter);
    }
}
