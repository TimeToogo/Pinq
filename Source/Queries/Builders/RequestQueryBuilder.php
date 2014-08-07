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
    final public function parseRequest(O\Expression $expression, $closureScopeType = null)
    {
        $scopeParser   = $this->scopeBuilder->buildScopeParser();
        $requestParser = $this->buildRequestParser();

        $this->interpretRequestQuery($expression, $scopeParser, $requestParser, $closureScopeType);

        return $this->buildRequestQuery(
                $scopeParser->getScope(),
                $requestParser->getRequest(),
                $this->buildParameterRegistry($scopeParser, $requestParser)
        );
    }

    /**
     * @param Queries\IScope             $scope
     * @param Queries\IRequest           $request
     * @param Queries\IParameterRegistry $parameters
     *
     * @return Queries\IRequestQuery
     */
    protected function buildRequestQuery(
            Queries\IScope $scope,
            Queries\IRequest $request,
            Queries\IParameterRegistry $parameters
    ) {
        return new Queries\RequestQuery($scope, $request, $parameters);
    }

    public function resolveRequest(O\Expression $expression)
    {
        $scopeResolver   = $this->scopeBuilder->buildScopeResolver();
        $requestResolver = $this->buildRequestResolver();

        $this->interpretRequestQuery($expression, $scopeResolver, $requestResolver);

        return $this->buildResolvedQuery($scopeResolver, $requestResolver);
    }

    protected function interpretRequestQuery(
            O\Expression $expression,
            Interpretations\IScopeInterpretation $scopeInterpretation,
            Interpretations\IRequestInterpretation $requestInterpretation,
            $closureScopeType = null
    ) {
        $scopeInterpreter        = $this->scopeBuilder->buildScopeInterpreter($scopeInterpretation, $closureScopeType);
        $requestQueryInterpreter = $this->buildRequestQueryInterpreter(
                $requestInterpretation,
                $scopeInterpreter,
                $closureScopeType
        );

        $requestQueryInterpreter->interpret($expression);
    }

    /**
     * @param Interpretations\IRequestInterpretation $requestInterpretation
     * @param IScopeInterpreter                      $scopeInterpreter
     * @param string|null                            $closureScopeType
     *
     * @return IRequestQueryInterpreter
     */
    protected function buildRequestQueryInterpreter(
            Interpretations\IRequestInterpretation $requestInterpretation,
            IScopeInterpreter $scopeInterpreter,
            $closureScopeType = null
    ) {
        return new RequestQueryInterpreter($requestInterpretation, $scopeInterpreter, $closureScopeType);
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
