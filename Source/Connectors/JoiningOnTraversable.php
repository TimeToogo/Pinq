<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Iterators;

/**
 * Implements the filtering API for a join / group join traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnTraversable implements Interfaces\IJoiningOnTraversable, Interfaces\IJoiningOnCollection
{
    /**
     * @var \Traversable
     */
    private $outerValues;

    /**
     * @var \Traversable
     */
    private $innerValues;

    /**
     * @var boolean
     */
    private $isGroupJoin;


    /**
     * @var callable
     */
    private $traversableFactory;

    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(\Traversable $outerValues, \Traversable $innerValues, $isGroupJoin, callable $traversableFactory)
    {
        $this->outerValues = $outerValues;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
        $this->traversableFactory = $traversableFactory;
    }

    public function on(callable $joiningOnFunction)
    {
        return new JoiningToConnector($this->onIteratorFactory($joiningOnFunction));
    }
    
    final protected function onIteratorFactory(callable $joiningOnFunction)
    {
        return function (callable $joiningFunction) use ($joiningOnFunction) {
            if ($this->isGroupJoin) {
                $iterator = new Iterators\CustomGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction);
            } else {
                $iterator = new Iterators\CustomJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction);
            }
            
            $traversableFactory = $this->traversableFactory;
            return $traversableFactory($iterator);
        };
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoiningToConnector($this->onEqualityTravserableFactory($outerKeyFunction, $innerKeyFunction));
    }
    
    final protected function onEqualityTravserableFactory(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return function (callable $joiningFunction) use ($outerKeyFunction, $innerKeyFunction) {
            if ($this->isGroupJoin) {
                $iterator = new Iterators\EqualityGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction);
            } else {
                $iterator = new Iterators\EqualityJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction);
            }
            
            $traversableFactory = $this->traversableFactory;
            return $traversableFactory($iterator);
        };
    }
}
