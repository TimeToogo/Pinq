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
    protected $ConstructJoinIteratorCallback;
    
    public function __construct(callable $ConstructJoinIteratorCallback)
    {
        $this->ConstructJoinIteratorCallback = $ConstructJoinIteratorCallback;
    }

    public function To(callable $JoinFunction)
    {
        $ConstructJoinIteratorCallback = $this->ConstructJoinIteratorCallback;
        return new Traversable($ConstructJoinIteratorCallback($JoinFunction));
    }

}
