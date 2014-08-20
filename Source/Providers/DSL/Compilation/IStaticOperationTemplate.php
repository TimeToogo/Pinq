<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Interface for a compiled operation query.
 *
*@author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IStaticOperationTemplate extends IOperationTemplate, IStaticQueryTemplate
{
    /**
     * {@inheritDoc}
     * @return ICompiledOperation
     */
    public function getCompiledQuery();
}
