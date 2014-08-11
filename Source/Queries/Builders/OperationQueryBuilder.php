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
                $operationParser->getOperation(),
                $this->buildParameterRegistry($scopeParser, $operationParser)
        );
    }

    /**
     * @param Queries\IScope             $scope
     * @param Queries\IOperation         $operation
     * @param Queries\IParameterRegistry $parameters
     *
     * @return Queries\IOperationQuery
     */
    protected function buildOperationQuery(
            Queries\IScope $scope,
            Queries\IOperation $operation,
            Queries\IParameterRegistry $parameters
    ) {
        return new Queries\OperationQuery($scope, $operation, $parameters);
    }

    public function resolveOperation(O\Expression $expression)
    {
        $scopeResolver     = $this->scopeBuilder->buildScopeResolver();
        $operationResolver = $this->buildOperationResolver();

        $this->interpretOperationQuery($expression, $scopeResolver, $operationResolver);

        return $this->buildResolvedQuery($scopeResolver, $operationResolver);
    }

    protected function interpretOperationQuery(
            O\Expression $expression,
            Interpretations\IScopeInterpretation $scopeInterpretation,
            Interpretations\IOperationInterpretation $operationInterpretation,
            $closureScopeType = null,
            $closureNamespace = null
    ) {
        $scopeInterpreter          = $this->scopeBuilder->buildScopeInterpreter(
                $scopeInterpretation,
                $closureScopeType,
                $closureNamespace
        );
        $operationQueryInterpreter = $this->buildOperationQueryInterpreter(
                $operationInterpretation,
                $scopeInterpreter,
                $closureScopeType,
                $closureNamespace
        );

        $operationQueryInterpreter->interpret($expression);
    }

    /**
     * @param Interpretations\IOperationInterpretation $operationInterpretation
     * @param IScopeInterpreter                        $scopeInterpreter
     * @param string|null                              $closureScopeType
     * @param string|null                              $closureNamespace
     *
     * @return IOperationQueryInterpreter
     */
    protected function buildOperationQueryInterpreter(
            Interpretations\IOperationInterpretation $operationInterpretation,
            IScopeInterpreter $scopeInterpreter,
            $closureScopeType = null,
            $closureNamespace = null
    ) {
        return new OperationQueryInterpreter($operationInterpretation, $scopeInterpreter, $closureScopeType, $closureNamespace);
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
