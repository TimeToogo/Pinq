<?php

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * The API for a cache implementation that can persist in memory variables
 * to be accessed later.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICacheAdapter
{
    /**
     * Saves the supplied value to the cache.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function save($key, $value);

    /**
     * Returns whether the supplied function has
     *
     * @param string $key
     * @return boolean
     */
    public function contains($key);

    /**
     * Attempt to get the cached value associated with the supplied key
     *
     * @param string $key
     * @return mixed
     */
    public function tryGet($key);

    /**
     * Removes the value associated with the supplied key
     *
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * Clears all cached values.
     *
     * @return void
     */
    public function clear();
}
