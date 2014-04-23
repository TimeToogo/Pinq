<?php

namespace Pinq\Providers;

use Pinq\Queries;

/**
 * The query provider is used to by the IQueryable as the data source
 * in which the query requests are all loaded from
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IQueryProvider
{
    /**
     * @return \Pinq\IQueryable
     */
    public function createQueryable(Queries\IScope $scope = null);

    /**
     * @return \Pinq\Parsing\IFunctionToExpressionTreeConverter
     */
    public function getFunctionToExpressionTreeConverter();

    /**
     * @return mixed
     */
    public function load(Queries\IRequestQuery $query);
}
