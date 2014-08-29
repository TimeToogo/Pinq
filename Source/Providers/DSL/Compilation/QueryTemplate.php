<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the query template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryTemplate implements IQueryTemplate
{
    /**
     * @var Queries\IQuery
     */
    protected $query;

    /**
     * @var Queries\IParameterRegistry
     */
    protected $parameters;

    /**
     * @var Parameters\ParameterRegistry
     */
    protected $structuralParameters;

    public function __construct(
            Queries\IQuery $query = null,
            Queries\IParameterRegistry $parameters,
            Parameters\ParameterRegistry $structuralParameters
    ) {
        $this->query                = $query;
        $this->parameters           = $parameters;
        $this->structuralParameters = $structuralParameters;
    }

    final public function getQuery()
    {
        return $this->query;
    }

    final public function getParameters()
    {
        return $this->parameters;
    }

    final public function getStructuralParameters()
    {
        return $this->structuralParameters;
    }

    public function resolveStructuralParameters(Queries\IResolvedParameterRegistry $parameterRegistry, &$hash)
    {
        $resolvedStructuralExpressions = $this->structuralParameters->resolve($parameterRegistry);
        $hash                          = implode('::', $resolvedStructuralExpressions->getHashesAsArray());

        return $resolvedStructuralExpressions;
    }
}
