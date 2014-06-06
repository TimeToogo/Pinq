<?php

namespace Pinq\Caching;

use Doctrine\Common\Cache\Cache;

/**
 * Adapter class for a doctring cache component that implements
 * \Doctrine\Common\Cache\Cache
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DoctrineCacheAdapter implements ICacheAdapter
{
    /**
     * The doctrine cache implementation
     *
     * @var Cache
     */
    private $doctrineCache;

    public function __construct(Cache $doctrineCache)
    {
        $this->doctrineCache = $doctrineCache;
    }

    public function save($key, $value)
    {
        $this->doctrineCache->save($key, $value);
    }

    public function tryGet($key)
    {
        $result = $this->doctrineCache->fetch($key);

        return $result === false ? null : $result;
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
        }
    }

}
