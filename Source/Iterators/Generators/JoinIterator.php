<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IJoinToIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinIterator extends IteratorGenerator implements IJoinToIterator
{
    use Common\JoinIterator;
    
    /**
     * @var \Traversable
     */
    protected $outerIterator;

    /**
     * @var \Traversable
     */
    protected $innerIterator;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator)
    {
        parent::__construct($outerIterator);
        self::__constructIterator();
        $this->outerIterator =& $this->iterator;
        $this->innerIterator = $innerIterator;
    }
    
    final protected function &iteratorGenerator(\Traversable $iterator)
    {
        $generator = $this->joinGenerator($iterator, $this->innerIterator, $this->projectionFunction);
        
        foreach($generator as $key => $value) {
            yield $key => $value;
            unset($value);
        }
    }
    
    abstract protected function joinGenerator(
            \Traversable $outerIterator, 
            \Traversable $innerIterator,
            callable $projectionFunction);
}
