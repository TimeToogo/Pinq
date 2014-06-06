<?php

namespace Pinq\Caching;

/**
 * Null cache implementation. Used if no other cache is supplied.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NullCache implements ICacheAdapter
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
    
    public function clear()
    {
        
    }
}
