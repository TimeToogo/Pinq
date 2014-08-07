<?php

namespace Pinq\Queries;

/**
 * An request query is a type of query for IQueryable, it represents
 * an value to retrieve from the supplied scope of the source values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQuery extends IQuery
{
    /**
     * @return IRequest
     */
    public function getRequest();
}
