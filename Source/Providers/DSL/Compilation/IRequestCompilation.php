<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Base interface for a request query undergoing compilation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestCompilation extends IQueryCompilation
{
    const IREQUEST_COMPILATION_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return ICompiledRequest
     */
    public function asCompiled();
}
