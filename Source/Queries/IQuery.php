<?php

namespace Pinq\Queries;

/**
 * Base interface for request / operation query types which
 * both act upon the supplied scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IQuery
{
    /**
     * The query scope
     * 
     * @return IScope
     */
    public function GetScope();
}
