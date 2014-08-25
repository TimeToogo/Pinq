<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface of a request query template.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestTemplate extends IQueryTemplate
{
    /**
     * Gets the request query object.
     *
     * @return Queries\IRequestQuery|null
     */
    public function getQuery();
}
