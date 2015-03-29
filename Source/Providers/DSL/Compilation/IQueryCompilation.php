<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Base interface for an request / operation query undergoing compilation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCompilation
{
    /**
     * Gets the parameter collection of the query compilation.
     *
     * @return Parameters\ParameterCollection
     */
    public function getParameters();

    /**
     * Returns the query compilation as the final compiled query.
     *
     * @return ICompiledQuery
     */
    public function asCompiled();
}
