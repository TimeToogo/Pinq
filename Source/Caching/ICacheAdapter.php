<?php

namespace Pinq\Caching;

/**
 * The API for a cache implementation that can persist in memory variables
 * to be accessed later.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ICacheAdapter
{
    /**
     * Whether the cache is namespaced.
     *
     * @return boolean
     */
    public function hasNamespace();

    /**
     * The namespace of the cache.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Returns a new cache implementation for a new namespace.
     *
     * @param string $namespace
     *
     * @return ICacheAdapter
     */
    public function forNamespace($namespace);

    /**
     * Returns a new cache implementation for the global namespace.
     *
     * @return ICacheAdapter
     */
    public function inGlobalNamespace();

    /**
     * Saves the supplied value to the cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function save($key, $value);

    /**
     * Returns whether the supplied function has
     *
     * @param string $key
     *
     * @return boolean
     */
    public function contains($key);

    /**
     * Attempt to get the cached value associated with the supplied key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function tryGet($key);

    /**
     * Removes the value associated with the supplied key
     *
     * @param string $key
     *
     * @return void
     */
    public function remove($key);

    /**
     * Clears all cached values under the namespace.
     *
     * @return void
     */
    public function clear();
}
