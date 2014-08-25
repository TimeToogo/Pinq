<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface of a operation query template.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationTemplate extends IQueryTemplate
{
    /**
     * Gets the operation query object.
     *
     * @return Queries\IOperationQuery|null
     */
    public function getQuery();
}
