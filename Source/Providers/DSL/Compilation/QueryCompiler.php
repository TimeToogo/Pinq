<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class for request/operation query compilers.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryCompiler
{
    /**
     * @var IScopeCompiler
     */
    protected $scopeCompiler;

    public function __construct(IScopeCompiler $scopeCompiler)
    {
        $this->scopeCompiler = $scopeCompiler;
    }

    /**
     * @param mixed                              $compilation
     * @param Queries\IQuery                     $query
     * @param Queries\IResolvedParameterRegistry $parameters
     *
     * @return void
     */
    protected function compileScope($compilation, Queries\IQuery $query, Queries\IResolvedParameterRegistry $parameters)
    {
        $this->scopeCompiler->compileScope($compilation, $query->getScope(), $parameters);
    }
}
