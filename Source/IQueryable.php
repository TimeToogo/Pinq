<?php

namespace Pinq;

/**
 * The queryable allows traversable query api, on an exteral query provider.
 * Supplied functions are converted to expression trees and are used to execute
 * equivalent querys on an external source.
 */
interface IQueryable extends ITraversable
{
    /**
     * The query provider for the implementation.
     * 
     * @return Providers\IQueryProvider
     */
    public function GetProvider();
    
    /**
     * The current query scope.
     * 
     * @return Queries\IScope
     */
    public function GetScope();
}
