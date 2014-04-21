<?php

namespace Pinq\Providers;

use \Pinq\Queries;

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
    public function CreateQueryable(Queries\IScope $Scope = null);
    
    /**
     * @return \Pinq\Parsing\IFunctionToExpressionTreeConverter
     */
    public function GetFunctionToExpressionTreeConverter();

    /**
     * @return mixed
     */
    public function Load(Queries\IRequestQuery $Query);
}
