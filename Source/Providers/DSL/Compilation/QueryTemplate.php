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
     * The parameter names of the query which affect the structure
     * of the compiled query.
     *
     * @var string[]
     */
    protected $structuralParameterNames;

    public function __construct(Queries\IParameterRegistry $parameters, array $structuralParameterNames)
    {
        $this->parameters = $parameters;
        $this->structuralParameterNames = $structuralParameterNames;
        sort($this->structuralParameterNames, SORT_STRING);
    }

    final public function getParameters()
    {
        return $this->parameters;
    }

    final public function getStructuralParameterNames()
    {
        return $this->structuralParameterNames;
    }

    public function getCompiledQueryHash(Queries\IResolvedParameterRegistry $parameters)
    {
        $structuralParameterValues = [];

        foreach($this->structuralParameterNames as $parameter) {
            $structuralParameterValues[$parameter] = $parameters[$parameter];
        }

        return $this->getStructuralParameterHash($structuralParameterValues);
    }

    protected function getStructuralParameterHash(array $structuralParameters)
    {
        return md5(serialize($structuralParameters));
    }
}