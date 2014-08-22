<?php

namespace Pinq\Providers;

use Pinq\Expressions as O;

/**
 * The query provider is used to by the IRepository as the data source
 * in which the query requests are all loaded from and all operation query
 * are executed against.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRepositoryProvider extends IQueryProvider
{
    /**
     * @return IQueryProvider
     */
    public function getQueryProvider();

    /**
     * @return Configuration\IRepositoryConfiguration
     */
    public function getConfiguration();

    /**
     * @param O\TraversalExpression $scopeExpression
     *
     * @return \Pinq\IRepository
     */
    public function createRepository(O\TraversalExpression $scopeExpression = null);

    /**
     * @param O\Expression $operationExpression
     *
     * @return void
     */
    public function execute(O\Expression $operationExpression);
}
