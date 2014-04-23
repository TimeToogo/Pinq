<?php

namespace Pinq;

/**
 * The queryable provides the traversable query API, on an exteral query provider.
 * Supplied functions are converted to expression trees and are used to execute
 * equivalent querys on the external source.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IQueryable extends ITraversable
{
    const IQUERYABLE_TYPE = __CLASS__;

    /**
     * The query provider for the implementation.
     *
     * @return Providers\IQueryProvider
     */
    public function getProvider();

    /**
     * The current query scope.
     *
     * @return Queries\IScope
     */
    public function getScope();
}
