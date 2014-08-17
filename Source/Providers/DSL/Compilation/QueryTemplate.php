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
     * @var Queries\IParameterRegistry
     */
    protected $parameters;

    /**
     * The parameters of the query which affect the structure of the compiled query.
     *
     * @var ParameterCollection
     */
    protected $structuralParameters;

    public function __construct(Queries\IParameterRegistry $parameters, ParameterCollection $structuralParameters)
    {
        $this->parameters = $parameters;
        $this->structuralParameters = $structuralParameters;
    }

    final public function getParameters()
    {
        return $this->parameters;
    }

    final public function getStructuralParameters()
    {
        return $this->structuralParameters;
    }

    public function getCompiledQueryHash(Queries\IResolvedParameterRegistry $parameters)
    {
        $structuralParameterValues = $this->structuralParameters->resolveParameters($parameters);

        return $this->getStructuralParameterHash($structuralParameterValues);
    }

    protected function getStructuralParameterHash(array $structuralParameters)
    {
        ksort($structuralParameters, \SORT_STRING);
        return md5(serialize($structuralParameters));
    }
}