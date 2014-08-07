<?php

namespace Pinq\Caching;

/**
 * Base class for cache implementations
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class CacheAdapter implements ICacheAdapter
{
    public function forNamespace($namespace)
    {
        return new NamespacedCache($this, $namespace);
    }
} 