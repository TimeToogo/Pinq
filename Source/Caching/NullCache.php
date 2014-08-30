<?php

namespace Pinq\Caching;

/**
 * Null cache implementation. Used if no other cache is supplied.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NullCache extends OneDimensionalCacheAdapter
{
    public function save($key, $value)
    {

    }

    public function tryGet($key)
    {
        return null;
    }

    public function contains($key)
    {
        return false;
    }

    public function remove($key)
    {

    }

    public function clear($namespace = null)
    {

    }

    public function clearInNamespace($namespace)
    {

    }
}
