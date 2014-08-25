<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Interface for a request / operation query that contains no
 * structural parameters and hence stores the fully compiled query.
 *
*@author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IStaticQueryTemplate extends IQueryTemplate
{
    /**
     * @return null
     */
    public function getQuery();

    /**
     * Gets the compiled query object.
     *
     * @return ICompiledQuery
     */
    public function getCompiledQuery();
}
