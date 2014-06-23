<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Standard\IIterator;

/**
 * Implementation of the adapter iterator for Pinq\Iterators\IIterator using the generator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IIteratorAdapter extends IteratorGenerator
{
    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }
    
    protected function &iteratorGenerator(\Traversable $iterator)
    {
        if(!($iterator instanceof IIterator)) {
            throw new \Pinq\PinqException('$iterator must be an instance of %s', IIterator::IITERATOR_TYPE);
        }
        
        $iterator->rewind();
        while($element = $iterator->fetch()) {
            yield $element[0] => $element[1];
        }
    }
}
