<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Interface of the operation query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationQueryProcessor extends IQueryProcessor
{
    /**
     * {@inheritDoc}
     * @return Queries\IOperationQuery
     */
    public function buildQuery();
}
