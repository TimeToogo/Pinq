<?php

namespace Pinq\Caching;

class NamespacedCache extends CacheAdapter implements INamespacedCacheAdapter
{
    /**
     * @var ICacheAdapter
     */
    private $innerCache;

    /**
     * @var string
     */
    private $namespace;

    public function __construct(ICacheAdapter $innerCache, $namespace)
    {
        $this->innerCache = $innerCache;
        $this->namespace  = $namespace;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getInnerCache()
    {
        return $this->innerCache;
    }

    public function forNamespace($namespace)
    {
        return new self($this->innerCache, $this->namespace . $namespace);
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

    public function clear($namespace = null)
    {
        $this->innerCache->clear($this->namespace . $namespace);
    }
}
