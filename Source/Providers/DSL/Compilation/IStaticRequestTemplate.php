<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Interface for a compiled request query.
 *
*@author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IStaticRequestTemplate extends IRequestTemplate, IStaticQueryTemplate
{
    /**
     * {@inheritDoc}
     * @return ICompiledRequest
     */
    public function getCompiledQuery();
}
