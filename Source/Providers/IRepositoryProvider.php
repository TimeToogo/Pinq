<?php

namespace Pinq\Providers;

use \Pinq\Queries;

/**
 * The query provider is used to by the IRepository as the data source
 * in which the query requests are all loaded from and all operation query
 * are executed against
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRepositoryProvider extends IQueryProvider
{
    /**
     * @return \Pinq\IRepository
     */
    public function CreateRepository(Queries\IScope $Scope = null);
    
    /**
     * @return void
     */
    public function Execute(Queries\IOperationQuery $Query);
}
