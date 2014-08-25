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
     * Gets the query object.
     *
     * @return Queries\IQuery|null
     */
    public function getQuery();

    /**
     * Gets the structural expression registry.
     *
     * @return Parameters\StructuralExpressionRegistry
     */
    public function getStructuralExpressions();

    /**
     * Resolves the structural expressions of the query template.
     *
     * @param Queries\IResolvedParameterRegistry $parameterRegistry
     * @param string                             $hash
     *
     * @return Parameters\ResolvedStructuralExpressionRegistry
     */
    public function resolveStructuralExpressions(Queries\IResolvedParameterRegistry $parameterRegistry, &$hash);
}
