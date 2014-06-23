<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the array iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayIterator extends Generator
{
    use Common\ArrayIterator;
    
    public function __construct(array $array)
    {
        parent::__construct();
        self::__constructIterator($array);
    }
    
    public function &getIterator()
    {
        foreach($this->array as $key => &$value) {
            yield $key => $value;
        }
    }
}
