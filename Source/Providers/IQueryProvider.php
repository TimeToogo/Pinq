<?php

namespace Pinq\Providers;

use \Pinq\Queries;

interface IQueryProvider
{
    /**
     * @return \Pinq\IQueryable
     */
    public function CreateQueryable(Queries\IQueryStream $QueryStream);
    
    /**
     * @return \Pinq\Parsing\IFunctionToExpressionTreeConverter
     */
    public function GetFunctionToExpressionTreeConverter();

    /**
     * @return IQueryScope
     */
    public function Scope(Queries\IQueryStream $QueryStream);
}
