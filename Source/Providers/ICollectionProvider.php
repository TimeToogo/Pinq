<?php

namespace Pinq\Providers;

use \Pinq\Queries;

interface ICollectionProvider extends IQueryProvider
{

    /**
     * @return void
     */
    public function Execute(Queries\IOperationQuery $Query);
}
