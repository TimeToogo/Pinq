<?php

namespace Pinq\Providers;

use \Pinq\Queries;

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
