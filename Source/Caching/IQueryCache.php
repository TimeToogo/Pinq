<?php

namespace Pinq\Caching;

use Pinq\Queries\IQuery;

/**
 * The API for a cache implementation to store the expression trees
 * of parsed functions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCache
{
    /**
     * Returns the underlying cache implementation.
     *
     * @return ICacheAdapter
     */
    public function getCacheAdapter();

    /**
     * Cache the supplied query associated with its hash.
     *
     * @param string $hash
     * @param IQuery $query
     *
     * @return void
     */
    public function save($hash, IQuery $query);

    /**
     * Attempt to get the cached query of the supplied hash.
     *
     * @param string $hash
     *
     * @return IQuery|null
     */
    public function tryGet($hash);

    /**
     * Removes the cached query with the associated hash.
     *
     * @param string $hash
     *
     * @return void
     */
    public function remove($hash);

    /**
     * Clears all cached queries for the supplied function reflection.
     *
     * @return void
     */
    public function clear();
}
