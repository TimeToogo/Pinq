<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Interface of the query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryProcessor
{
    /**
     * @return IScopeProcessor
     */
    public function getScopeProcessor();

    /**
     * @return Queries\IQuery
     */
    public function buildQuery();
}
