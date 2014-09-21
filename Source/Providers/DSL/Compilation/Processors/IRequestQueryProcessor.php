<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Interface of the request query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQueryProcessor extends IQueryProcessor
{
    /**
     * {@inheritDoc}
     * @return Queries\IRequestQuery
     */
    public function buildQuery();
}
