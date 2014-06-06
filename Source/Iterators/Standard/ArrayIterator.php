<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the array iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
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
    
    final protected function doFetch(&$key, &$value)
    {
        return false !== (list($key, $value) = each($this->array));
    }
}
