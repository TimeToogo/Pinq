<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\SetOperations\ISetFilter;

/**
 * Implementation of the set operation iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SetOperationIterator extends IteratorGenerator
{
    use Common\SetOperations\SetOperationIterator;
    
    public function __construct(\Traversable $iterator, ISetFilter $setFilter)
    {
        parent::__construct($iterator);
        self::__constructIterator($setFilter);
    }
    
    protected function &iteratorGenerator(\Traversable $iterator)
    {
        $this->setFilter->initialize();
        
        foreach($iterator as $key => &$value) {
            if($this->setFilter->filter($key, $value)) {
                yield $key => $value;
            }
        }
    }
}
