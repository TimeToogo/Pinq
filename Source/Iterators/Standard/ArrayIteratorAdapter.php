<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\IAdapterIterator;

/**
 * Implementation of the adapter iterator for \ArrayIterator using the fetch method
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayIteratorAdapter extends Iterator implements IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\ArrayIterator $arrayIterator)
    {
        parent::__construct();
        self::__constructIterator($arrayIterator);
    }

    protected function doRewind()
    {
        parent::doRewind();

        $this->iterator->rewind();
    }

    protected function doFetch()
    {
        $arrayIterator = $this->iterator;

        if ($arrayIterator->valid()) {
            $key = $arrayIterator->key();

            //Get value by ref
            $element = [$key, &$arrayIterator[$key]];

            $arrayIterator->next();

            return $element;
        }
    }
}
