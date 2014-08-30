<?php

namespace Pinq\Tests\Integration\Caching;

/**
 * Simple array access cache implementation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayAccessCacheImplementation implements \ArrayAccess, \IteratorAggregate
{
    private $array = [];

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->array);
    }

    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }
}
