<?php

namespace Pinq\Caching;

use Doctrine\Common\Cache\CacheProvider as DoctrineCacheProvider;

/**
 * Adapter class for a doctrine cache component that implements
 * \Doctrine\Common\Cache\Cache
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DoctrineCache extends CacheAdapter
{
    /**
     * The doctrine cache implementation.
     *
     * @var DoctrineCacheProvider
     */
    private $doctrineCache;

    public function __construct(DoctrineCacheProvider $doctrineCache, $namespace = null)
    {
        parent::__construct($namespace);
        $this->doctrineCache = $doctrineCache;
    }

    public function forNamespace($namespace)
    {
        return new self($this->doctrineCache, $namespace);
    }

    public function inGlobalNamespace()
    {
        return new self($this->doctrineCache);
    }

    protected function withNamespace()
    {
        $this->doctrineCache->setNamespace($this->namespace);

        return $this->doctrineCache;
    }

    public function save($key, $value)
    {
        $this->withNamespace()->save($key, $value);
    }

    public function tryGet($key)
    {
        return $this->withNamespace()->contains($key) ? $this->withNamespace()->fetch($key) : null;
    }

    public function contains($key)
    {
        return $this->withNamespace()->contains($key);
    }

    public function remove($key)
    {
        $this->withNamespace()->delete($key);
    }

    public function clear()
    {
        if (!$this->hasNamespace()) {
            $this->doctrineCache->flushAll();
        } else {
            $this->withNamespace()->deleteAll();
        }
    }
}
