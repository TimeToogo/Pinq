<?php

namespace Pinq\Iterators;

/**
 * Implementation of array iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayIterator extends Iterator
{
    /**
     * @var array
     */
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }
    
    /**
     * @return array
     */
    final public function getArrayCopy()
    {
        return $this->array;
    }

    public function doRewind()
    {
        reset($this->array);
    }
    
    final protected function doFetch(&$key, &$value)
    {
        return false !== (list($key, $value) = each($this->array));
    }
}
