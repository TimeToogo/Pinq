<?php

namespace Pinq\Caching;

use Doctrine\Common\Cache\Cache;

/**
 * Adapter class for a doctrine cache component that implements
 * \Doctrine\Common\Cache\Cache
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DoctrineCacheAdapter extends OneDimensionalCacheAdapter
{
    /**
     * The doctrine cache implementation.
     *
     * @var Cache
     */
    private $doctrineCache;

    public function __construct(Cache $doctrineCache)
    {
        parent::__construct();
        $this->doctrineCache = $doctrineCache;
    }

    public function save($key, $value)
    {
        $this->doctrineCache->save($key, $value);
    }

    public function tryGet($key)
    {
        return $this->doctrineCache->contains($key) ? $this->doctrineCache->fetch($key) : null;
    }

    public function contains($key)
    {
        return $this->doctrineCache->contains($key);
    }

    public function remove($key)
    {
        $this->doctrineCache->delete($key);
    }

    public function clear()
    {
        if ($this->doctrineCache instanceof \Doctrine\Common\Cache\CacheProvider) {
            $this->doctrineCache->deleteAll();
        } else {
            throw new \Pinq\PinqException(
                    'Cannot clear cache %s: cache does not support %s deleting all elements.',
                    get_class($this->doctrineCache));
        }
    }

    public function clearInNamespace($namespace)
    {
        if ($this->doctrineCache instanceof \Doctrine\Common\Cache\CacheProvider) {
            $originalNamespace = $this->doctrineCache->getNamespace();
            $this->doctrineCache->setNamespace($namespace);
            $this->doctrineCache->deleteAll();
            $this->doctrineCache->setNamespace($originalNamespace);
        } else {
            throw new \Pinq\PinqException(
                    'Cannot clear cache %s: cache does not support %s deleting elements in an namespace.',
                    get_class($this->doctrineCache));
        }
    }
}
