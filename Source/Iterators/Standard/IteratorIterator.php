<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Base class for wrapper iterators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IteratorIterator extends Iterator implements \Iterator
{
    /**
     * @var IIterator
     */
    protected $iterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct();
        $this->iterator = $iterator;
    }
    
    /**
     * @return IIterator
     */
    final public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function doRewind()
    {
        $this->iterator->rewind();
    }
}
