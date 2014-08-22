<?php

namespace Pinq\Providers;

use Pinq\Expressions as O;

/**
 * The query provider is used to by the IQueryable as the data source
 * in which the query requests are all loaded from
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryProvider
{
    /**
     * @return Configuration\IQueryConfiguration
     */
    public function getConfiguration();

    /**
     * @return Utilities\IQueryResultCollection|null
     */
    public function getQueryResultCollection();

    /**
     * @param O\TraversalExpression $scopeExpression
     *
     * @return \Pinq\IQueryable
     */
    public function createQueryable(O\TraversalExpression $scopeExpression = null);

    /**
     * @param O\Expression $requestExpression
     *
     * @return mixed
     */
    public function load(O\Expression $requestExpression);
}
