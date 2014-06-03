<?php

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with all the matching inner values in a traversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CustomGroupJoinIterator extends CustomJoinIteratorBase
{
    /**
     * @var callable
     */
    private $traversableFactory;
    
    public function __construct(
            \Traversable $outerIterator, 
            \Traversable $innerIterator,
            callable $joinOnFunction,
            callable $joiningFunction,
            callable $traversableFactory = null)
    {
        parent::__construct($outerIterator, $innerIterator, $joinOnFunction, $joiningFunction);
        
        $this->traversableFactory = $traversableFactory ?: \Pinq\Traversable::factory();
    }
    
    protected function getInnerGroupValuesIterator(callable $innerValueFilterFunction)
    {
        $traversableFactory = $this->traversableFactory;
        $groupTraversable = $traversableFactory(
                new FilterIterator($this->innerValues, $innerValueFilterFunction));
        
        return new ArrayIterator([$groupTraversable]);
    }
}
