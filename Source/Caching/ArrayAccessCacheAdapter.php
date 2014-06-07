<?php

namespace Pinq\Caching;

/**
 * Adapter class for a cache that implements \ArrayAccess
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayAccessCacheAdapter implements ICacheAdapter
{
    /**
     * The cache object implementing array access
     *
     * @var \ArrayAccess
     */
    private $arrayAccess;

    public function __construct(\ArrayAccess $innerCache)
    {
        $this->arrayAccess = $innerCache;
    }

    public function save($key, $value)
    {
        $this->arrayAccess[$key] = $value;
    }
    
    public function contains($key)
    {
        return isset($this->arrayAccess[$key]);
    }

    public function tryGet($key)
    {
        return isset($this->arrayAccess[$key]) ? $this->arrayAccess[$key] : null;
    }

    public function remove($key)
    {
        unset($this->arrayAccess[$key]);
    }

    public function clear()
    {
        if (method_exists($this->arrayAccess, 'clear')) {
            $this->arrayAccess->clear();
        } elseif ($this->arrayAccess instanceof \Traversable) {
            $keys = array_keys(iterator_to_array($this->arrayAccess, true));

            foreach ($keys as $key) {
                unset($this->arrayAccess[$key]);
            }
        }
    }
}
