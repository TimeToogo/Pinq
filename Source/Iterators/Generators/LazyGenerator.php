<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for a lazy generator, initialized upon rewind.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyGenerator extends IteratorGenerator
{
    public function __construct(IGenerator $iterator)
    {
        parent::__construct($iterator);
    }
    
    final protected function &iteratorGenerator(IGenerator $iterator)
    {
        $loadedIterator = $this->initializeGenerator($iterator);
        
        foreach($loadedIterator as $key => &$value) {
            yield $key => $value;
        }
    }
    
    /**
     * @return \Traversable
     */
    abstract protected function initializeGenerator(IGenerator $innerIterator);
}
