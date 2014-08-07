<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base interface for a request / operation query template.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryTemplate
{
    /**
     * Gets the parameter registry.
     *
     * @return Queries\IParameterRegistry
     */
    public function getParameters();

    /**
     * Gets the parameter names which affect the structure of the compiled query.
     *
     * @return string[]
     */
    public function getStructuralParameterNames();

    /**
     * Returns a unique string representing the compiled query structure.
     *
     * @param Queries\IResolvedParameterRegistry $parameters
     *
     * @return string
     */
    public function getCompiledQueryHash(Queries\IResolvedParameterRegistry $parameters);
}
