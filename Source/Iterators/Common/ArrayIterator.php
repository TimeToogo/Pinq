<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the array iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait ArrayIterator
{
    /**
     * @var array
     */
    protected $array;

    public function __constructIterator(array &$array)
    {
        $this->array =& $array;
    }

    /**
     * @return array
     */
    final public function getArrayCopy()
    {
        return $this->array;
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
