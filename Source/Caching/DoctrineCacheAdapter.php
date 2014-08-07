<?php

namespace Pinq\Caching;

use Doctrine\Common\Cache\Cache;

/**
 * Adapter class for a doctrine cache component that implements
 * \Doctrine\Common\Cache\Cache
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DoctrineCacheAdapter extends CacheAdapter
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

    public function clear($namespace = null)
    {
        if ($this->doctrineCache instanceof \Doctrine\Common\Cache\CacheProvider && $namespace === null) {
            $this->doctrineCache->deleteAll();
        } else {
            throw new \Pinq\PinqException(
                    'Cannot clear cache %s: cache does not support %s',
                    get_class($this->doctrineCache),
                    $namespace === null ? ' clearing.' : ' namespaced clearing.');
        }
    }

}
