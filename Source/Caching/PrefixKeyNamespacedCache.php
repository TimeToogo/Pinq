<?php

namespace Pinq\Caching;

/**
 * Wrapper cache implementation that performs namespacing via prefixed keys.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PrefixKeyNamespacedCache extends CacheAdapter implements INamespacedCacheAdapter
{
    /**
     * @var OneDimensionalCacheAdapter
     */
    protected $innerCache;

    public function __construct(OneDimensionalCacheAdapter $innerCache, $namespace)
    {
        parent::__construct($namespace);
        $this->innerCache = $innerCache;
    }

    public function getInnerCache()
    {
        return $this->innerCache;
    }

    public function inGlobalNamespace()
    {
        return $this->innerCache->inGlobalNamespace();
    }

    public function forNamespace($namespace)
    {
        return $this->innerCache->forNamespace($namespace);
    }

    public function save($key, $value)
    {
        $this->innerCache->save($this->namespace . $key, $value);
    }

    public function contains($key)
    {
        return $this->innerCache->contains($this->namespace . $key);
    }

    public function tryGet($key)
    {
        return $this->innerCache->tryGet($this->namespace . $key);
    }

    public function remove($key)
    {
        $this->innerCache->remove($this->namespace . $key);
    }

    public function clear()
    {
        $this->innerCache->clearInNamespace($this->namespace);
    }
}
