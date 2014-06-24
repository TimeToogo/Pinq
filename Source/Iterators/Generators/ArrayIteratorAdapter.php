<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the adapter iterator for \ArrayIterator using the generator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayIteratorAdapter extends Generator implements \Pinq\Iterators\IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\ArrayIterator $arrayIterator)
    {
        parent::__construct();
        self::__constructIterator($arrayIterator);
    }
    
    public function &getIterator()
    {
        foreach($this->iterator as $key => &$value) {
            yield $key => $value;
        }
    }

}
