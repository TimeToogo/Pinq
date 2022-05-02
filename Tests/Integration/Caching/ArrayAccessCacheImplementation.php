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

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->array);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->array);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->array[$offset]);
    }
}
