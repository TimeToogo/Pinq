<?php

namespace Pinq\Caching;

/**
 * Base class for cache implementation that represent a key value store
 * that only supports one dimensional storage. Hence namespacing is performed
 * via prefixing of keys.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OneDimensionalCacheAdapter extends CacheAdapter
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function forNamespace($namespace)
    {
        return new PrefixKeyNamespacedCache($this, $namespace);
    }

    /**
     * Returns a new cache implementation for the global namespace.
     *
     * @return ICacheAdapter
     */
    public function inGlobalNamespace()
    {
        return $this;
    }

    /**
     * Clears all cached values in the supplied namespace key prefix.
     *
     * @param string $namespace
     *
     * @return void
     */
    abstract public function clearInNamespace($namespace);
}
