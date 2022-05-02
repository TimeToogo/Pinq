<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IAdapterIterator;

/**
 * Implementation of the adapter iterator for \ArrayIterator using the generator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayIteratorAdapter extends Generator implements IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\ArrayIterator $arrayIterator)
    {
        parent::__construct();
        self::__constructIterator($arrayIterator);
    }

    public function &getIterator(): \Traversable
    {
        foreach ($this->iterator as $key => &$value) {
            yield $key => $value;
        }
    }

}
