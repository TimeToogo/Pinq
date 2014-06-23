<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the reindexer iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReindexedIterator extends IteratorIterator
{
    /**
     * @var int
     */
    private $index = 0;
    
    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }
    
    public function doRewind()
    {
        $this->index = 0;
        parent::doRewind();
    }

    protected function doFetch()
    {
        if($element = $this->iterator->fetch()) {
            $element[0] = $this->index++;
            
            return $element;
        }
    }
}
