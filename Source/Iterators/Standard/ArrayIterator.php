<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the array iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayIterator extends Iterator
{
    use Common\ArrayIterator;

    public function __construct(array $array)
    {
        parent::__construct();
        self::__constructIterator($array);
    }

    public function doRewind()
    {
        reset($this->array);
    }

    final protected function doFetch()
    {
        $key = key($this->array);
        if ($key === null) {
            return null;
        }
        next($this->array);

        return [$key, &$this->array[$key]];
    }
}
