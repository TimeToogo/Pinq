<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;

/**
 * Implementation of the request query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestQueryBuilder extends QueryBuilder implements IRequestQueryBuilder
{
    final public function parseRequest(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeParser   = $this->scopeBuilder->buildScopeParser();
        $requestParser = $this->buildRequestParser();

        $this->interpretRequestQuery($expression, $scopeParser, $requestParser, $evaluationContext);

        return $this->buildRequestQuery(
                $scopeParser->getScope(),
                $requestParser->getRequest()
        );
    }

    /**
     * @param Queries\IScope   $scope
     * @param Queries\IRequest $request
     *
     * @return Queries\IRequestQuery
     */
    protected function buildRequestQuery(
            Queries\IScope $scope,
            Queries\IRequest $request
    ) {
        return new Queries\RequestQuery($scope, $request);
    }

    public function resolveRequest(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeResolver   = $this->scopeBuilder->buildScopeResolver();
        $requestResolver = $this->buildRequestResolver();

        $this->interpretRequestQuery($expression, $scopeResolver, $requestResolver, $evaluationContext);

        return $this->buildResolvedQuery($scopeResolver, $requestResolver);
    }

    protected function interpretRequestQuery(
            O\Expression $expression,
            Interpretations\IScopeInterpretation $scopeInterpretation,
            Interpretations\IRequestInterpretation $requestInterpretation,
            O\IEvaluationContext $evaluationContext = null
    ) {
        $scopeInterpreter        = $this->scopeBuilder->buildScopeInterpreter(
                $scopeInterpretation,
                $evaluationContext
        );
        $requestQueryInterpreter = $this->buildRequestQueryInterpreter(
                $requestInterpretation,
                $scopeInterpreter,
                $evaluationContext
        );

        $requestQueryInterpreter->interpret($expression);
    }

    /**
     * @param Interpretations\IRequestInterpretation $requestInterpretation
     * @param IScopeInterpreter                      $scopeInterpreter
     * @param O\IEvaluationContext                   $evaluationContext
     *
     * @return IRequestQueryInterpreter
     */
    protected function buildRequestQueryInterpreter(
            Interpretations\IRequestInterpretation $requestInterpretation,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        return new RequestQueryInterpreter($requestInterpretation, $scopeInterpreter, $evaluationContext);
    }

    /**
     * @return Interpretations\IRequestParser
     */
    protected function buildRequestParser()
    {
        return new Interpretations\RequestParser($this->functionInterpreter);
    }

    /**
     * @return Interpretations\IRequestResolver
     */
    protected function buildRequestResolver()
    {
        return new Interpretations\RequestResolver($this->functionInterpreter);
    }
}
