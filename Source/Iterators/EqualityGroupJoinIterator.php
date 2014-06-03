<?php

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with all the matching inner values in a traversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityGroupJoinIterator extends EqualityJoinIteratorBase
{
    /**
     * @var callable
     */
    private $traversableFactory;
    
    public function __construct(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction, 
            callable $joiningFunction,
            callable $traversableFactory = null)
    {
        parent::__construct(
                $outerIterator, 
                $innerIterator, 
                $outerKeyFunction, 
                $innerKeyFunction, 
                $joiningFunction);
        
        $this->traversableFactory = $traversableFactory ?: \Pinq\Traversable::factory();
    }
    
    protected function getInnerGroupValueIterator(Utilities\OrderedMap $innerGroup)
    {
        $traversableFactory = $this->traversableFactory;
        return new \ArrayIterator([$traversableFactory($innerGroup)]);
    }
}
