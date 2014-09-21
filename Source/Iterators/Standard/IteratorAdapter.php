<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\IAdapterIterator;

/**
 * Implementation of the adapter iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IteratorAdapter extends Iterator implements IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
        self::__constructIterator($iterator);
    }

    public function doRewind()
    {
        parent::doRewind();

        $this->iterator->rewind();
    }

    protected function doFetch()
    {
        $iterator = $this->iterator;

        if ($iterator->valid()) {
            $element = [$iterator->key(), $iterator->current()];

            $iterator->next();

            return $element;
        }
    }
}
