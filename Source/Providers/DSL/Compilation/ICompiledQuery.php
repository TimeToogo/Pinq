<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Base interface for a compiled request / operation query.
 *
 * The compiled query is a parametrized query ready to be executed
 * against a set of parameters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ICompiledQuery
{
    /**
     * Gets the parameter registry of the compiled query.
     *
     * @return Parameters\ParameterRegistry
     */
    public function getParameterRegistry();
}
