<?php

namespace Pinq;

/**
 * The repository allows traversable query and mutable collection api, on an external provider.
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
