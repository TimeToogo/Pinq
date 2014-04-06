<?php

namespace Pinq\Providers;

use \Pinq\Queries;

interface IQueryProvider
{
    /**
     * @return \Pinq\IQueryable
     */
    public function CreateQueryable(Queries\IScope $Scope);
    
    /**
     * @return \Pinq\Parsing\IFunctionToExpressionTreeConverter
     */
    public function GetFunctionToExpressionTreeConverter();

    /**
     * @return mixed
     */
    public function Load(Queries\IRequestQuery $Query);
}
