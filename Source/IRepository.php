<?php

namespace Pinq;

/**
 * The repository provides the mutable collection API on a queryable.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRepository extends IQueryable, ICollection
{
    /**
     * The repository provider for the implementation.
     * 
     * @return Providers\IRepositoryProvider
     */
    public function GetProvider();
}
