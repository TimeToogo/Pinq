<?php 

namespace Pinq;

/**
 * Implements the result API for a join / group join traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningToTraversable implements IJoiningToTraversable
{
    /**
     * @var callable
     */
    protected $constructJoinIteratorCallback;
    
    public function __construct(callable $constructJoinIteratorCallback)
    {
        $this->constructJoinIteratorCallback = $constructJoinIteratorCallback;
    }
    
    public function to(callable $joinFunction)
    {
        $constructJoinIteratorCallback = $this->constructJoinIteratorCallback;
        
        return new Traversable($constructJoinIteratorCallback($joinFunction));
    }
}