<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Base interface for a operation query undergoing compilation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationCompilation extends IQueryCompilation
{
    const IOPERATION_COMPILATION_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return ICompiledOperation
     */
    public function asCompiled();
}
